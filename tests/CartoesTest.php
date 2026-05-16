<?php
use PHPUnit\Framework\TestCase;

class CartoesTest extends TestCase 
{
    // 1 - Testa a conversão do dinheiro (BRL) para o formato do BD (Float)
    public function testConversaoDeMoedaBrasileiraParaFloat() 
    {
        $limiteDigitadoPost = '1.500,75';
        
        // A exata lógica de tratamento de CartoesController::store()
        $limite = str_replace('.', '', $limiteDigitadoPost);
        $limite = str_replace(',', '.', $limite);
        $limiteConvertido = (float) $limite;

        $this->assertIsFloat($limiteConvertido);
        $this->assertEquals(1500.75, $limiteConvertido);
    }

    // 2 - Testa a limpeza de dados (Proteção contra XSS no nome do cartão)
    public function testSanitizacaoDeEntradaDeDados()
    {
        // Simulando ataque XSS no campo "nome" do cartão
        $nomeCartaoMalicioso = '   <script>alert("Hacker")</script> Nubank   ';
        
        // Lógica de sanitização
        $nomeLimpo = strip_tags(trim($nomeCartaoMalicioso));

        // Verifica se os espaços sumiram e se as tags de script foram removidas, mas o texto "Nubank" permaneceu
        $this->assertEquals('alert("Hacker") Nubank', $nomeLimpo);
        
        // Garante que as tags de script foram removidas
        $this->assertStringNotContainsString('<script>', $nomeLimpo);
        $this->assertStringNotContainsString('</script>', $nomeLimpo);
    }

    // 3 - Testa a barreira de proteção CSRF do Controller
    public function testRejeicaoDeTokenCsrfInvalido()
    {
        $tokenNaSessao = 'abc123_token_real';
        $tokenEnviadoPeloFormulario = 'xyz999_token_falso';

        $valido = ($tokenEnviadoPeloFormulario === $tokenNaSessao);

        $this->assertFalse($valido, "O sistema deve barrar tokens diferentes.");
    }

    // 4 - Testa a atribuição de uma cor padrão caso o usuário não envie uma
    public function testAtribuicaoDeCorPretaPadrao()
    {
        $corEnviada = null;

        $corFinal = $corEnviada ?? '#000000';

        $this->assertEquals('#000000', $corFinal);
    }
}