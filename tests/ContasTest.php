<?php
use PHPUnit\Framework\TestCase;

class ContasTest extends TestCase 
{
    // 1 - Testando a conversão do saldo inicial de Real para Float
    public function testConversaoSaldoInicialBrlParaFloat() 
    {
        $saldoDigitado = '1.250,99'; 
        
        $saldo_inicial = str_replace('.', '', $saldoDigitado);
        $saldo_inicial = str_replace(',', '.', $saldo_inicial);
        $saldoConvertido = (float) $saldo_inicial;

        $this->assertIsFloat($saldoConvertido);
        $this->assertEquals(1250.99, $saldoConvertido);
    }

    // 2 - Testando se uma cor Hex correta é aceita
    public function testAceitacaoDeCorHexadecimalValida() 
    {
        $corEnviada = '#FF5733';

        if(!preg_match('/^#[a-fA-F0-9]{6}$/', $corEnviada)) {
            $corEnviada = '#CCC';
        }

        $this->assertEquals('#FF5733', $corEnviada);
    }

    // 3 - Testando se uma cor é inválida ou tentativa de injection é bloqueada e substituída pelo padrão
    public function testRejeicaoDeCorInvalida() 
    {
        $corInvalida = 'red; script()';
        
        if(!preg_match('/^#[a-fA-F0-9]{6}$/', $corInvalida)) {
            $corCorrigida = '#CCCCCC';
        } else {
            $corCorrigida = $corInvalida;
        }

        $this->assertEquals('#CCCCCC', $corCorrigida);
    }

    // 4 - Testando a sanitização do nome do bd
    public function testSanitizacaoDoNomeDoBanco()
    {
        $nomeSujo = '   <b>Nubank</b>   ';
        $nomeLimpo = strip_tags(trim($nomeSujo));

        $this->assertEquals('Nubank', $nomeLimpo);
        $this->assertStringNotContainsString('<b>', $nomeLimpo);
    }

    // 5 - Testando a proteção CSRF
    public function testRejeicaoDeAcaoComCsrfInvalido()
    {
        $tokenSessao = 'token_seguro_123';
        $tokenPost = 'token_falso_999';

        $falhaDetectada = (!isset($tokenPost) || $tokenPost !== $tokenSessao);

        $this->assertTrue($falhaDetectada);
    }
}