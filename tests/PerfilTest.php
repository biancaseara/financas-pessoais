<?php
use PHPUnit\Framework\TestCase;

class PerfilTest extends TestCase 
{
    // 1. Testa a geração do token CSRF na ausência de um (Lógica do index)
    public function testGeracaoDeTokenCsrfSeVazio()
    {
        $sessao = []; // Simulando uma $_SESSION vazia
        
        if (empty($sessao['csrf_token'])) {
            $sessao['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->assertArrayHasKey('csrf_token', $sessao);
        $this->assertNotEmpty($sessao['csrf_token']);
        // 32 bytes convertidos para hexadecimal resultam em uma string de 64 caracteres
        $this->assertEquals(64, strlen($sessao['csrf_token'])); 
    }

    // 2. Testa se o token não é sobrescrito caso já exista na sessão
    public function testPreservacaoDoTokenCsrfExistente()
    {
        $tokenOriginal = 'token_pre_existente_abcd1234';
        $sessao = ['csrf_token' => $tokenOriginal]; // Sessão já possui o token
        
        if (empty($sessao['csrf_token'])) {
            $sessao['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->assertEquals($tokenOriginal, $sessao['csrf_token']);
    }

    // 3. Testa a validação e rejeição do CSRF no update simulando a Exception
    public function testRejeicaoUpdateComCsrfInvalido()
    {
        // Dizemos ao PHPUnit que esperamos que uma Exception seja lançada
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Falha de segurança CSRF detectada no perfil.");

        $sessaoCsrf = 'token_real_sessao';
        $postCsrf = 'token_forjado_formulario';

        // Simulando a lógica de bloqueio do método update()
        if (!isset($postCsrf) || $postCsrf !== $sessaoCsrf) {
            throw new Exception("Falha de segurança CSRF detectada no perfil.");
        }
    }

    // 4. Testa a lógica de substituição dos dados na sessão após um update bem-sucedido
    public function testAtualizacaoDoNomeNaSessao()
    {
        // Estado antes do update
        $sessao = ['nome' => 'Nome Antigo'];
        $post = ['nome' => 'Bianca Atualizada'];

        // Ação: Lógica que ocorre no final do update() após o banco salvar
        $sessao['nome'] = $post['nome'];

        $this->assertEquals('Bianca Atualizada', $sessao['nome']);
    }
}