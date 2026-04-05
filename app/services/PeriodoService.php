<?php
// Normaliza periodos para consultas indexadas por data/hora
class PeriodoService
{
    public static function normalizarData($valor, $padrao)
    {
        $valor = trim((string)$valor);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor) !== 1) {
            return $padrao;
        }
        return $valor;
    }

    public static function periodoIndexado($inicio, $fim)
    {
        $inicioNormalizado = self::normalizarData($inicio, date('Y-m-01'));
        $fimNormalizado = self::normalizarData($fim, date('Y-m-t'));
        if ($inicioNormalizado > $fimNormalizado) {
            [$inicioNormalizado, $fimNormalizado] = [$fimNormalizado, $inicioNormalizado];
        }

        return [
            'inicio_data' => $inicioNormalizado,
            'fim_data' => $fimNormalizado,
            'inicio_dt' => $inicioNormalizado . ' 00:00:00',
            'fim_dt' => date('Y-m-d H:i:s', strtotime($fimNormalizado . ' +1 day'))
        ];
    }
}
