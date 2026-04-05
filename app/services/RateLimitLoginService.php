<?php
// Controle simples de tentativas de login para reduzir risco de forca bruta
class RateLimitLoginService
{
    private const MAX_TENTATIVAS = 5;
    private const JANELA_SEGUNDOS = 900; // 15 minutos
    private const BLOQUEIO_SEGUNDOS = 900; // 15 minutos
    private static $tabelaDisponivel = null;

    public function obterStatus($ip, $email)
    {
        if (!$this->tabelaDisponivel()) {
            return [
                'bloqueado' => false,
                'segundos_restantes' => 0
            ];
        }

        $registro = $this->buscarRegistro($ip, $email);
        if (!$registro) {
            return [
                'bloqueado' => false,
                'segundos_restantes' => 0
            ];
        }

        $agora = time();
        $bloqueadoAte = !empty($registro['bloqueado_ate']) ? strtotime((string)$registro['bloqueado_ate']) : 0;
        if ($bloqueadoAte > $agora) {
            return [
                'bloqueado' => true,
                'segundos_restantes' => $bloqueadoAte - $agora
            ];
        }

        return [
            'bloqueado' => false,
            'segundos_restantes' => 0
        ];
    }

    public function registrarFalha($ip, $email)
    {
        if (!$this->tabelaDisponivel()) {
            return;
        }

        $db = Conexao::obter();
        $chave = $this->gerarChave($ip, $email);
        $registro = $this->buscarPorChave($chave);
        $agora = new DateTimeImmutable('now');
        $janelaInicio = $agora->modify('-' . self::JANELA_SEGUNDOS . ' seconds');

        if (!$registro) {
            $this->inserirNovoRegistro($chave, $ip, $email, 1, null);
            return;
        }

        $ultimoEvento = !empty($registro['ultimo_evento']) ? new DateTimeImmutable((string)$registro['ultimo_evento']) : null;
        $tentativas = (int)$registro['tentativas'];

        if (!$ultimoEvento || $ultimoEvento < $janelaInicio) {
            $this->atualizarRegistro($chave, $ip, $email, 1, null);
            return;
        }

        $tentativas++;
        $bloqueadoAte = null;
        if ($tentativas >= self::MAX_TENTATIVAS) {
            $bloqueadoAte = $agora->modify('+' . self::BLOQUEIO_SEGUNDOS . ' seconds')->format('Y-m-d H:i:s');
        }

        $this->atualizarRegistro($chave, $ip, $email, $tentativas, $bloqueadoAte);
    }

    public function registrarSucesso($ip, $email)
    {
        if (!$this->tabelaDisponivel()) {
            return;
        }

        $chave = $this->gerarChave($ip, $email);
        $stmt = Conexao::obter()->prepare('DELETE FROM tentativas_login WHERE chave = :chave');
        $stmt->execute([':chave' => $chave]);
    }

    public function limparExpirados()
    {
        if (!$this->tabelaDisponivel()) {
            return;
        }

        $stmt = Conexao::obter()->prepare(
            'DELETE FROM tentativas_login
             WHERE ultimo_evento < DATE_SUB(NOW(), INTERVAL 2 DAY)'
        );
        $stmt->execute();
    }

    private function buscarRegistro($ip, $email)
    {
        return $this->buscarPorChave($this->gerarChave($ip, $email));
    }

    private function buscarPorChave($chave)
    {
        $stmt = Conexao::obter()->prepare(
            'SELECT chave, tentativas, bloqueado_ate, ultimo_evento
             FROM tentativas_login
             WHERE chave = :chave
             LIMIT 1'
        );
        $stmt->execute([':chave' => $chave]);
        return $stmt->fetch() ?: null;
    }

    private function inserirNovoRegistro($chave, $ip, $email, $tentativas, $bloqueadoAte)
    {
        $stmt = Conexao::obter()->prepare(
            'INSERT INTO tentativas_login (chave, ip, email, tentativas, bloqueado_ate, ultimo_evento)
             VALUES (:chave, :ip, :email, :tentativas, :bloqueado_ate, NOW())'
        );
        $stmt->execute([
            ':chave' => $chave,
            ':ip' => $ip,
            ':email' => $email,
            ':tentativas' => $tentativas,
            ':bloqueado_ate' => $bloqueadoAte
        ]);
    }

    private function atualizarRegistro($chave, $ip, $email, $tentativas, $bloqueadoAte)
    {
        $stmt = Conexao::obter()->prepare(
            'UPDATE tentativas_login
             SET ip = :ip,
                 email = :email,
                 tentativas = :tentativas,
                 bloqueado_ate = :bloqueado_ate,
                 ultimo_evento = NOW()
             WHERE chave = :chave'
        );
        $stmt->execute([
            ':chave' => $chave,
            ':ip' => $ip,
            ':email' => $email,
            ':tentativas' => $tentativas,
            ':bloqueado_ate' => $bloqueadoAte
        ]);
    }

    private function gerarChave($ip, $email)
    {
        $ip = trim((string)$ip);
        $emailNormalizado = strtolower(trim((string)$email));
        return hash('sha256', $ip . '|' . $emailNormalizado);
    }

    private function tabelaDisponivel()
    {
        if (self::$tabelaDisponivel !== null) {
            return self::$tabelaDisponivel;
        }

        try {
            $stmt = Conexao::obter()->query("SHOW TABLES LIKE 'tentativas_login'");
            self::$tabelaDisponivel = (bool)$stmt->fetchColumn();
        } catch (Throwable $erro) {
            self::$tabelaDisponivel = false;
        }

        return self::$tabelaDisponivel;
    }
}
