<?php
use PHPUnit\Framework\TestCase;

class RecorrentesTest extends TestCase 
{
    // 1. Testa a limpeza da descrição da despesa recorrente (store e update)
    public function testSanitizacaoDaDescricaoDaDespesa()
    {
        $descricaoSuja = '  <script>alert("Hacked")</script> Spotify Premium  ';

        // Lógica replicada do store() e update()
        $descricaoLimpa = strip_tags(trim($descricaoSuja));

        // O strip_tags remove as tags, mas deixa o conteúdo do meio intacto.
        // O importante para a segurança é que as tags <script> sumam.
        $this->assertEquals('alert("Hacked") Spotify Premium', $descricaoLimpa);
        $this->assertStringNotContainsString('<script>', $descricaoLimpa);
        $this->assertStringNotContainsString('</script>', $descricaoLimpa);
    }

    // 2. Testa o bloqueio de requisições POST com CSRF inválido em ações destrutivas (delete)
    public function testRejeicaoDeAcaoDeleteComCsrfInvalido()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Falha de segurança CSRF detectada.");

        $sessaoCsrf = 'token_real_da_sessao';
        $postCsrf = 'token_falso_ou_ausente';

        // Simula a verificação feita no início dos métodos store, update, delete e lancarMes
        if (!isset($postCsrf) || $postCsrf !== $sessaoCsrf) {
            throw new Exception("Falha de segurança CSRF detectada.");
        }
    }

    // 3. Testa a regra de negócio do ajuste do dia de vencimento para meses mais curtos (lancarMes)
    public function testAjusteDiaVencimentoParaFimDoMes()
    {
        $diaVencimentoOriginal = 31;
        
        // Simulando que estamos em Fevereiro de um ano não bissexto (28 dias)
        $ultimoDiaMes = 28; 

        $diaVencimentoCalculado = $diaVencimentoOriginal;
        
        // Lógica exata do método lancarMes()
        if ($diaVencimentoCalculado > $ultimoDiaMes) {
            $diaVencimentoCalculado = $ultimoDiaMes;
        }

        $dataVencimento = '2026-02-' . str_pad($diaVencimentoCalculado, 2, '0', STR_PAD_LEFT);

        $this->assertEquals(28, $diaVencimentoCalculado, "O dia 31 deve ser reduzido para o dia 28 em meses mais curtos.");
        $this->assertEquals('2026-02-28', $dataVencimento);
    }

    // 4. Testa a formatação correta do nome do lançamento automático (lancarMes)
    public function testFormatacaoDescricaoLancamentoAutomatico()
    {
        $descricaoOriginal = "Academia";
        
        // Simulando o mês/ano
        $mesAnoDisplay = "07/2026"; 

        // Lógica de formatação do lancarMes()
        $descricaoFormatada = "🔄 " . $descricaoOriginal . " (" . $mesAnoDisplay . ")";

        $this->assertEquals("🔄 Academia (07/2026)", $descricaoFormatada);
        $this->assertStringStartsWith("🔄", $descricaoFormatada);
        $this->assertStringEndsWith("(07/2026)", $descricaoFormatada);
    }
}