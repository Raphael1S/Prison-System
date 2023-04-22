<?php

namespace Raphael\Prison;

# https://github.com/Raphael1S/Prison-System

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Position;
require_once("Update10.php");

class Blaze extends PluginBase implements Listener {

    private $prisao = null;
    private $jogadoresPresos = [];
    private $data;
    private $verificarTempoTaskId = null;

    public function onEnable() {
        UpdateVersion1($this);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("§eAutoClicker habilitado! @ Raphael S.");
        $this->saveResource("data.yml");
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        $this->jogadoresPresos = $this->data->get("jogadoresPresos", []);
        $this->prisao = $this->data->get("prisao");    
        if (count($this->jogadoresPresos) === 1) {
        $this->verificarTempoTaskId = $this->getScheduler()->scheduleRepeatingTask(new VerificarTempoTask($this), 20 * 60)->getTaskId();
    }
   }


public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
    switch(strtolower($command->getName())) {
        case "prender":
                    if (!$sender->hasPermission("prender.prender")) {
                    $sender->sendMessage("§cVocê não pode executar esse comando.");
                    return false;
                }
            if(count($args) >= 2) {
                $jogador = $this->getServer()->getPlayer($args[0]);
                if($jogador instanceof Player) {
                    $tempoUnix = intval($args[1]);
                    if($tempoUnix > 0) {
                        $this->prenderJogador($jogador, $tempoUnix);
                        $this->getServer()->broadcastMessage("§eO jogador " . $jogador->getName() . " foi preso por " . $tempoUnix . " segundos.");
                        return true;
                    } else {
                        $sender->sendMessage("O tempo precisa ser maior do que zero.");
                        return true;
                    }
                } elseif(strtolower($args[0]) === "liberar" && isset($args[1])) {
                    $jogador = $this->getServer()->getPlayer($args[1]);
                    if($jogador instanceof Player && isset($this->jogadoresPresos[$jogador->getName()])) {
    $this->getServer()->broadcastMessage("§aO jogador " . $jogador->getName() . " foi liberado da prisão.");     
    $jogador = $this->getServer()->getPlayerExact($jogador->getName());
    $level = $jogador->getLevel();
    $spawn = $level->getSpawnLocation();
    $jogador->teleport($spawn);
    $jogador->sendMessage(TextFormat::GREEN . "Você foi libertado da prisão.");
    unset($this->jogadoresPresos[$jogador->getName()]);
    $this->data->set("jogadoresPresos", $this->jogadoresPresos);
    $this->data->save();
                        return true;
                    } else {
                        $sender->sendMessage("§cJogador não encontrado ou não está preso.");
                        return true;
                    }
                } else {
                    $sender->sendMessage("§cUso correto: /prender <jogador> <tempo em segundos> ou /prender liberar <jogador>");
                    return true;
                }
            } elseif(count($args) == 1 && strtolower($args[0]) === "setar" && $sender instanceof Player) {
                $this->prisao = $sender->getPosition();
                $this->data->set("prisao", ["x" => $this->prisao->getX(), "y" => $this->prisao->getY(), "z" => $this->prisao->getZ(), "mundo" => $this->prisao->getLevel()->getName()]);

                $this->data->save();
                $this->prisao = $this->data->get("prisao");
                $sender->sendMessage("§aPosição da prisão definida.");
                return true;
            } else {
                return false;
            }
            break;
        default:
            return false;
            break;
    }
}
    
    public function onPlayerChat(\pocketmine\event\player\PlayerChatEvent $event): void
{
    $jogador = $event->getPlayer();
    if (isset($this->jogadoresPresos[$jogador->getName()])) {
        $event->setCancelled();
        $jogador->sendMessage(TextFormat::RED . "Você não pode falar enquanto estiver preso.");
    }
}

public function onPlayerCommandPreprocess(PlayerCommandPreprocessEvent $event) {
    $jogador = $event->getPlayer();
    if (isset($this->jogadoresPresos[$jogador->getName()])) {
        $event->setCancelled();
        $jogador->sendMessage(TextFormat::RED . "Você não pode executar comandos enquanto estiver preso.");
    }
}
    
public function onPlayerJoin(PlayerJoinEvent $event): void
{
    $jogador = $event->getPlayer();
    if (isset($this->jogadoresPresos[$jogador->getName()])) {
        $prisao = $this->getServer()->getLevelByName($this->data->get("prisao")["mundo"]);
        if ($prisao !== null) {
            $prisaoPos = new Position($this->prisao['x'], $this->prisao['y'], $this->prisao['z'], $this->getServer()->getLevelByName($this->prisao['mundo']));
$pos = $prisaoPos->asVector3();
$jogador->teleport($pos, 0, 0);

            $jogador->sendMessage(TextFormat::RED . "Você está preso. Não pode sair da prisão.");
        }
    }
}




private function prenderJogador(Player $jogador, int $tempoUnix) {
    if (count($this->jogadoresPresos) === 0) {
        $this->verificarTempoTaskId = $this->getScheduler()->scheduleRepeatingTask(new VerificarTempoTask($this), 20 * 60)->getTaskId();
    }
    $this->jogadoresPresos[$jogador->getName()] = ["tempo" => $tempoUnix, "inicio" => time()];
    $this->data->set("jogadoresPresos", $this->jogadoresPresos);
    $this->data->save();
    $this->prender = $this->data->get("prisao");
    $prisaoPos = new Position($this->prender["x"], $this->prender["y"], $this->prender["z"], $this->getServer()->getLevelByName($this->prender["mundo"]));
$pos = $prisaoPos->asVector3();
$jogador->teleport($pos, 0, 0);
    $jogador->sendMessage(TextFormat::RED . "Você foi preso por " . $tempoUnix . " segundos.");
}
    
public function verificarPresos() {
    if (count($this->jogadoresPresos) === 0) {
        $this->getLogger()->info("§cNão há jogadores presos, desativando procura...");
        $this->getScheduler()->cancelTask($this->verificarTempoTaskId);

        return;
        }
        $this->getLogger()->info("§eVerificando tempo dos presos...");
    foreach($this->jogadoresPresos as $nome => $dados) {
        $tempoRestante = $dados["tempo"] - (time() - $dados["inicio"]);
        if($tempoRestante <= 0) {
            $jogador = $this->getServer()->getPlayerExact($nome);
            if($jogador instanceof Player && $jogador->isOnline()) {
                $level = $jogador->getLevel();
                $spawn = $level->getSpawnLocation();
                $jogador->teleport($spawn);
                $jogador->sendMessage(TextFormat::GREEN . "O seu tempo de prisão acabou.");
                unset($this->jogadoresPresos[$nome]);
            }
        }
    }
    $this->data->set("jogadoresPresos", $this->jogadoresPresos);
    $this->data->save();
}
}

class VerificarTempoTask extends Task {

    private $plugin;

    public function __construct(Blaze $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick) {
        
        $this->plugin->verificarPresos();
}
}
