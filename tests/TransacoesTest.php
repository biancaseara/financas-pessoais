<?php
use PHPUnit\Framework\TestCase;

class TransacoesTest extends TestCase 
{
    // 1. Testa a conversão do valor digitado na interface (formato BR) para Float (padrão DB)
    public function testConversaoMonetariaParaFloat()
    {
        $valorPost = '3.540,99';

        // Lógica de conversão do store() e update()
        $valorTratado = str_replace('.', '', $valorPost);
        $valorTratado = str_replace(',', '.', $valorTratado);
        $valorTratado = (float) $valorTratado;

        $this->assertIsFloat($valorTratado);
        $this->assertEquals(3540.99, $valorTratado);
    }

    // 2. Testa se a categoria é ignorada (anulada) quando o tipo for Transferência
    public function testAnulacaoDeCategoriaEmTransferencias()
    {
        $postTipoTransacao = 'Transferencia';
        $postIdCategoria = 5; // O usuário pode ter mandado uma categoria no POST sem querer

        // Lógica do store()
        $id_categoria_final = ($postTipoTransacao == 'Transferencia') ? null : $postIdCategoria;

        $this->assertNull($id_categoria_final, "Transferências não devem ter categoria vinculada.");
    }

    // 3. Testa a matemática e a geração de descrições das parcelas do Cartão de Crédito
    public function testLogicaDeParcelamentoDeCartaoDeCredito()
    {
        $valorTotal = 1500.00; // Valor já convertido
        $parcelas = 3;
        $descricaoOriginal = "Compra na Amazon";
        
        $valor_parcela = $valorTotal / $parcelas;
        $descricoesGeradas = [];

        for ($i = 0; $i < $parcelas; $i++) {
            $desc_parcelada = $descricaoOriginal;
            if ($parcelas > 1) {
                $num_parcela = $i + 1;
                $desc_parcelada .= " ({$num_parcela}/{$parcelas})";
            }
            $descricoesGeradas[] = $desc_parcelada;
        }

        $this->assertEquals(500.00, $valor_parcela);
        $this->assertCount(3, $descricoesGeradas);
        $this->assertEquals("Compra na Amazon (1/3)", $descricoesGeradas[0]);
        $this->assertEquals("Compra na Amazon (3/3)", $descricoesGeradas[2]);
    }

    // 4. Testa a projeção de datas (adição de meses) para faturas futuras
    public function testProjecaoDeDatasParaParcelasFuturas()
    {
        $dataTransacao = '2026-07-15'; // Compra feita no meio de julho
        $parcelas = 3;
        $datasGeradas = [];
        $mesesFatura = [];

        for ($i = 0; $i < $parcelas; $i++) {
            $novaData = date('Y-m-d', strtotime("+$i month", strtotime($dataTransacao)));
            $dataFatura = date('Y-m', strtotime($novaData));
            
            $datasGeradas[] = $novaData;
            $mesesFatura[] = $dataFatura;
        }

        // Verifica os dias exatos das transações projetadas
        $this->assertEquals('2026-07-15', $datasGeradas[0]);
        $this->assertEquals('2026-08-15', $datasGeradas[1]);
        $this->assertEquals('2026-09-15', $datasGeradas[2]);

        // Verifica o padrão "Ano-Mês" gerado para buscar/criar a Fatura
        $this->assertEquals('2026-07', $mesesFatura[0]);
        $this->assertEquals('2026-09', $mesesFatura[2]);
    }

    // 5. Testa a formatação correta da string de pagamento de fatura
    public function testFormatacaoDescricaoPagamentoFatura()
    {
        $fatura = [
            'nome_cartao' => 'Nubank',
            'mes_ano' => '2026-07'
        ];

        // Lógica exata do pagarFatura()
        $descricao = "Pagamento Fatura: " . $fatura['nome_cartao'] . " (" . $fatura['mes_ano'] . ")";

        $this->assertEquals("Pagamento Fatura: Nubank (2026-07)", $descricao);
    }
}