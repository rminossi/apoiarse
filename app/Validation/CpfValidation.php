<?php

namespace App\Validation;

use Illuminate\Validation\Validator;

class CpfValidation
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $value);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula os dígitos verificadores
        for ($i = 9; $i < 11; $i++) {
            $dv = 0;
            for ($j = 0; $j < $i; $j++) {
                $dv += $cpf[$j] * (($i + 1) - $j);
            }
            $dv = ((10 * $dv) % 11) % 10;
            if ($cpf[$i] != $dv) {
                return false;
            }
        }

        return true;
    }
}
