<h1 align="center">PrisonSystem</h1>

<h1 align="center">Um plugin de Prisão para PocketMine 3.</h1>
    </a>
<div align="center">
	<a href="https://www.blazehosting.com.br/discord">
        <img src="https://img.shields.io/badge/Discord-7289DA?style=for-the-badge&logo=discord&logoColor=white" alt="discord">
</a>

## Uso

Antes de mais nada, adicione o plugin à pasta de plugins e, em seguida, siga as etapas necessárias para configurá-lo adequadamente.

| Uso | Descrição |
| --- | --- |
| prender.prender | A permissão para executar o comando, em breve uma configuração. |
| /prender setar | Especifique o local da prisão. |
| /prender **"Jogador"** **"Tempo"** | Especifique o Jogador e o tempo em segundos da prisão. |
| /prender liberar **"Jogador"** | Liberte um Jogador da prisão. |

## Como o plugin funciona?

Quando um jogador é preso, ele é instantaneamente teletransportado para a área designada da prisão, onde é proibido executar comandos ou enviar mensagens no chat do servidor. Se o jogador sair e tentar entrar novamente no servidor, será imediatamente teletransportado novamente para a prisão até que o tempo de detenção termine. Quando o tempo de detenção termina, o jogador é teletransportado para o Spawn do Mundo da prisão. Por isso, é recomendável que você crie uma prisão em seu Lobby ou Hub. Uma sugestão é construir uma prisão de bloco invisível em seu lobby e definir essa área como a prisão do servidor.

## Otimização

Em termos de otimização, cada vez que o plugin é ativado, é verificado se há jogadores presos na data.yml. Se houver jogadores presos, a tarefa que verifica o tempo desses jogadores é ativada. Caso contrário, a tarefa não é ativada.
Enquanto a tarefa estiver em execução, ela continuamente verifica se há jogadores presos. Se durante a execução da tarefa, um jogador for liberado e não houver mais jogadores presos, a tarefa é interrompida.
Quando um jogador é preso através do comando, a tarefa é ativada novamente.
