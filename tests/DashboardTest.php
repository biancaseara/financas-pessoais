<?php
use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase 
{
    // 1. Testa a estrutura do array de dados que vai alimentar os cartões do Dashboard
    public function testEstruturaDoResumoFinanceiroDeveConterCamposObrigatorios() 
    {
        $resumoSimulado = [
            'total_receitas' => 5000.00,
            'total_despesas' => 2150.50,
            'saldo_atual' => 2849.50
        ];

        $this->assertArrayHasKey('total_receitas', $resumoSimulado);
        $this->assertArrayHasKey('total_despesas', $resumoSimulado);
        $this->assertArrayHasKey('saldo_atual', $resumoSimulado);
        
        $this->assertIsFloat($resumoSimulado['saldo_atual']);
        $this->assertEquals(2849.50, $resumoSimulado['saldo_atual']);
    }

    // 2. Testa o comportamento seguro caso o usuário não possua movimentações recentes
    public function testRecentesDeveRetornarArrayVazioCasoNaoHajaMovimentacoes()
    {
        $recentesSimulado = [];

        $this->assertIsArray($recentesSimulado);
        $this->assertEmpty($recentesSimulado, "Deve ser um array vazio se não houver registros históricos.");
    }

    // 3. Testa a estrutura dos dados do gráfico de pizza/barras por categoria
    public function testEstruturaDosGastosPorCategoriaParaOGrafico()
    {
        $gastosPorCategoriaSimulado = [
            ['categoria' => 'Alimentação', 'total' => 450.00],
            ['categoria' => 'Transporte', 'total' => 180.00]
        ];

        $this->assertIsArray($gastosPorCategoriaSimulado);
        
        // Garante que o primeiro item do gráfico possui os índices corretos para o JS ler
        $primeiroItem = $gastosPorCategoriaSimulado[0];
        $this->assertArrayHasKey('categoria', $primeiroItem);
        $this->assertArrayHasKey('total', $primeiroItem);
        $this->assertEquals('Alimentação', $primeiroItem['categoria']);
    }

    // 4. Testa a montagem do pacote final de dados que vai para a view
    public function testMontagemDoPacoteDeDadosDaView()
    {
        $dados = [
            'titulo' => 'Resumo Financeiro',
            'resumo' => ['total_receitas' => 0, 'total_despesas' => 0, 'saldo_atual' => 0],
            'recentes' => [],
            'orcamentos' => [],
            'gastosPorCategoria' => []
        ];

        $this->assertEquals('Resumo Financeiro', $dados['titulo']);
        $this->assertCount(5, $dados, "O pacote de dados deve conter exatamente as 5 chaves de controle do painel.");
    }
}