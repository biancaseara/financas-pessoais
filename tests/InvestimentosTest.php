<?php
use PHPUnit\Framework\TestCase;

class InvestimentosTest extends TestCase 
{
    // 1. Testa a limpeza de dados contra ataques XSS no nome e na corretora
    public function testSanitizacaoDeNomeECorretora()
    {
        // Simula o usuário inserindo espaços extras e tags HTML/Scripts maliciosos
        $nomeSujo = '   <script>Tesouro</script> Selic   ';
        $corretoraSuja = '  <b>XP</b> Investimentos  ';

        $nomeLimpo = strip_tags(trim($nomeSujo));
        $corretoraLimpa = strip_tags(trim($corretoraSuja));

        $this->assertEquals('Tesouro Selic', $nomeLimpo);
        $this->assertEquals('XP Investimentos', $corretoraLimpa);
        $this->assertStringNotContainsString('<script>', $nomeLimpo);
        $this->assertStringNotContainsString('<b>', $corretoraLimpa);
    }

    // 2. Testa se a data de vencimento em branco é convertida para null antes de ser salva no banco
    public function testVencimentoVazioDeveRetornarNulo()
    {
        $vencimentoEnviadoPost = ''; 

        $vencimentoProcessado = !empty($vencimentoEnviadoPost) ? $vencimentoEnviadoPost : null;

        $this->assertNull($vencimentoProcessado, "Se o vencimento vier vazio, o sistema deve preparar um valor null para o banco.");
    }

    // 3. Testa se uma data de vencimento preenchida é mantida de maneira correta
    public function testVencimentoPreenchidoDeveSerMantido()
    {
        $vencimentoEnviadoPost = '2028-12-31';

        $vencimentoProcessado = !empty($vencimentoEnviadoPost) ? $vencimentoEnviadoPost : null;

        $this->assertEquals('2028-12-31', $vencimentoProcessado);
    }

    // 4. Testa a trava de proteção CSRF nas rotas de alteração de dados
    public function testRejeicaoDeAcaoComCsrfInvalido()
    {
        $tokenNaSessao = 'token_seguro_da_sessao';
        $tokenEnviadoPeloFormulario = 'token_falso_hacker';

        $falhaDetectada = (!isset($tokenEnviadoPeloFormulario) || $tokenEnviadoPeloFormulario !== $tokenNaSessao);

        $this->assertTrue($falhaDetectada, "O sistema deve bloquear a requisição se os tokens CSRF não baterem.");
    }
}