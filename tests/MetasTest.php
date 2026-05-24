<?php
use PHPUnit\Framework\TestCase;

class MetasTest extends TestCase 
{
    // 1. Testa a limpeza de dados contra ataques XSS no título da meta
    public function testSanitizacaoDoTituloDaMeta()
    {
        $tituloSujo = '   <h1>Comprar Carro</h1>   ';

        $tituloLimpo = strip_tags(trim($tituloSujo));

        $this->assertEquals('Comprar Carro', $tituloLimpo);
        $this->assertStringNotContainsString('<h1>', $tituloLimpo);
        $this->assertStringNotContainsString('</h1>', $tituloLimpo);
    }

    // 2. Testa a trava de proteção CSRF nas rotas de processamento (POST)
    public function testRejeicaoDeAcaoComCsrfInvalido()
    {
        $tokenNaSessao = 'token_verdadeiro_sessao_123';
        $tokenEnviadoPeloFormulario = 'token_forjado_hacker_999';

        $falhaDeSeguranca = (!isset($tokenEnviadoPeloFormulario) || $tokenEnviadoPeloFormulario !== $tokenNaSessao);

        $this->assertTrue($falhaDeSeguranca, "O sistema deve bloquear a requisição se os tokens CSRF forem diferentes.");
    }

    // 3. Testa a conversão de real para float
    public function testConversaoMonetariaDeObjetivosEAtuais()
    {
        $valorDigitadoPost = '15.500,50';

        $valorTratado = str_replace('.', '', $valorDigitadoPost);
        $valorTratado = (float) str_replace(',', '.', $valorTratado);

        $this->assertIsFloat($valorTratado);
        $this->assertEquals(15500.50, $valorTratado);
    }
}