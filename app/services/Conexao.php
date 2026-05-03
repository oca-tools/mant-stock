<?php
class Conexao
{
    private static $instancia = null;

    public static function obter()
    {
        if (self::$instancia instanceof PDO) {
            return self::$instancia;
        }

        $config = require __DIR__ . '/../config/config.php';
        $db = $config['db'] ?? [];

        $host = getenv('DB_HOST') ?: ($db['host'] ?? '127.0.0.1');
        $nome = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: ($db['banco'] ?? ($db['nome'] ?? ($db['database'] ?? 'estoque_manutencao'))));
        $usuario = getenv('DB_USER') ?: (getenv('DB_USERNAME') ?: ($db['usuario'] ?? ($db['user'] ?? 'mantstock_user')));

        $senhaEnv = getenv('DB_PASS');
        if ($senhaEnv === false || $senhaEnv === null || $senhaEnv === '') {
            $senhaEnv = getenv('DB_PASSWORD');
        }
        $senha = ($senhaEnv !== false && $senhaEnv !== null) ? $senhaEnv : ($db['senha'] ?? ($db['pass'] ?? ($db['password'] ?? '')));

        $charset = $db['charset'] ?? 'utf8mb4';
        $dsn = "mysql:host={$host};dbname={$nome};charset={$charset}";

        self::$instancia = new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$instancia;
    }
}
