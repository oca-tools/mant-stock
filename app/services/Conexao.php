<?php
// Servico de conexao com o banco de dados
class Conexao
{
    private static $instancia = null;

    public static function obter()
    {
        if (self::$instancia === null) {
            $config = require __DIR__ . '/../config/config.php';
            $db = $config['db'];
            $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['banco'] . ';charset=' . $db['charset'];
            self::$instancia = new PDO($dsn, $db['usuario'], $db['senha'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
        return self::$instancia;
    }
}
