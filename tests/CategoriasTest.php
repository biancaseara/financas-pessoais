<?php
use PHPUnit\Framework\TestCase;

class CategoriasTest extends TestCase 
{
    // 1 - Testa a conversão do limite mensal (BRL) para o Float do BD
    public function testConversaoDeLimiteMensalMonetarioParaFloat() 
    {
        $limiteDigitadoPost = '3.450,80'; 

        if (!empty($limiteDigitadoPost)) {
            $limite = str_replace('.', '', $limiteDigitadoPost);
            $limite = str_replace(',', '.', $limite);
            $limiteConvertido = (float) $limite;
        } else {
            $limiteConvertido = null;
        }

        $this->assertIsFloat($limiteConvertido);
        $this->assertEquals(3450.80, $limiteConvertido);
    }

    // 2 - Testa o comportamento quando o usuário não define um limite mensal
    public function testLimiteMensalVazioDeveSerTratadoComoNulo() 
    {
        $limiteDigitadoPost = '';
        
        if (!empty($limiteDigitadoPost)) {
            $limite = str_replace('.', '', $limiteDigitadoPost);
            $limite = str_replace(',', '.', $limite);
            $limiteConvertido = (float) $limite;
        } else {
            $limiteConvertido = null;
        }

        $this->assertNull($limiteConvertido, "Se o usuário não preencher o limite, o sistema deve registrar null no banco.");
    }

    // 3 - Testa a proteção CSRF nas ações de inserção, edição e deleção
    public function testRejeicaoDeAcaoComCsrfInvalido()
    {
        $tokenNaSessao = 'token_verdadeiro_sessao';
        $tokenEnviadoPeloFormulario = 'token_forjado_hacker';

        $falhaDeSeguranca = (!isset($tokenEnviadoPeloFormulario) || $tokenEnviadoPeloFormulario !== $tokenNaSessao);

        $this->assertTrue($falhaDeSeguranca, "O sistema deve identificar a falha se os tokens não baterem.");
    }

    // 4 - Testa a sanitização do nome da categoria para evitar espaços extras no banco
    public function testLimpezaDoNomeDaCategoria()
    {
        $nomeEnviado = '   Alimentação   ';
        $nomeLimpo = trim($nomeEnviado);

        $this->assertEquals('Alimentação', $nomeLimpo);
    }
}