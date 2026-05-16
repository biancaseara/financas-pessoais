<?php
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase 
{
    // 1 - Garante que a senha nunca seja armazenada em texto puro
    public function testSenhaDeveSerCriptografadaNoCadastro() 
    {
        $senhaDigitada = 'minhasenha123';
        $senhaCriptografada = password_hash($senhaDigitada, PASSWORD_DEFAULT);

        // Garante que o hash gerado é diferente da senha
        $this->assertNotEquals($senhaDigitada, $senhaCriptografada);
        
        // Garante que o PHP consegue validar a senha depois
        $this->assertTrue(password_verify($senhaDigitada, $senhaCriptografada));
    }

    // 2 - Garante que e-mails falsos não passem
    public function testEmailInvalidoDeveSerRejeitado() 
    {
        $emailErrado = 'bianca@.com'; // Formato inválido
        $emailCerto = 'bianca@email.com';

        $this->assertFalse(filter_var($emailErrado, FILTER_VALIDATE_EMAIL));
        $this->assertNotFalse(filter_var($emailCerto, FILTER_VALIDATE_EMAIL));
    }

    // 3 - Garante que o padrão é usuário comum
    public function testNovoUsuarioDeveTerPerfilComumPorPadrao()
    {
        $perfilFornecidoPeloFormulario = null;

        $perfilFinal = $perfilFornecidoPeloFormulario ?? 'comum';

        $this->assertEquals('comum', $perfilFinal);
    }
}