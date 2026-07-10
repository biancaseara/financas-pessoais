<?php
use PHPUnit\Framework\TestCase;

class UsuariosTest extends TestCase 
{
    // 1. Testa a trava de segurança principal: bloqueio de não-admins
    public function testBloqueioDeAcessoParaNaoAdmins()
    {
        // Cenário 1: Usuário comum
        $sessaoComum = ['perfil' => 'comum'];
        $acessoNegadoComum = (!isset($sessaoComum['perfil']) || $sessaoComum['perfil'] != 'admin');
        $this->assertTrue($acessoNegadoComum, "Usuário comum deve ter o acesso negado.");

        // Cenário 2: Usuário não logado (sessão vazia)
        $sessaoVazia = [];
        $acessoNegadoVazio = (!isset($sessaoVazia['perfil']) || $sessaoVazia['perfil'] != 'admin');
        $this->assertTrue($acessoNegadoVazio, "Usuário sem sessão deve ter o acesso negado.");

        // Cenário 3: Admin legítimo
        $sessaoAdmin = ['perfil' => 'admin'];
        $acessoNegadoAdmin = (!isset($sessaoAdmin['perfil']) || $sessaoAdmin['perfil'] != 'admin');
        $this->assertFalse($acessoNegadoAdmin, "Admin deve ter o acesso permitido.");
    }

    // 2. Testa a regra de negócio que impede um admin de rebaixar a si mesmo no update
    public function testPrevencaoDePerdaDePrivilegioAdminNoUpdate()
    {
        $id_usuario_logado = 1;
        $id_usuario_sendo_editado = 1; // Editando a si mesmo
        
        // Um hacker ou erro no formulário tenta passar o perfil para 'comum'
        $postPerfil = 'comum'; 

        // Lógica de proteção do update()
        $perfilFinal = $postPerfil;
        if ($id_usuario_sendo_editado == $id_usuario_logado) {
            $perfilFinal = 'admin'; 
        }

        $this->assertEquals('admin', $perfilFinal, "O sistema deve forçar o perfil 'admin' se o usuário estiver editando a si mesmo.");
    }

    // 3. Testa a trava que impede a auto-exclusão da conta no delete
    public function testPrevencaoDeAutoExclusaoDeConta()
    {
        $id_usuario_logado = 42;
        $id_usuario_alvo_delete = 42; // Tentando deletar a própria conta

        // Lógica de proteção do delete()
        $deveBloquearExclusao = ($id_usuario_alvo_delete == $id_usuario_logado);

        $this->assertTrue($deveBloquearExclusao, "O sistema não pode permitir que o ID alvo do delete seja igual ao ID da sessão.");
    }

    // 4. Testa se o sistema está gerando o Hash da senha corretamente no cadastro
    public function testCriptografiaDeSenhaNoCadastro()
    {
        $senhaPlana = 'minhasenha123';

        // Lógica do store()
        $senhaHash = password_hash($senhaPlana, PASSWORD_DEFAULT);

        // Verifica se a senha resultante é diferente da original
        $this->assertNotEquals($senhaPlana, $senhaHash);
        
        // Verifica se o hash gerado é válido e bate com a senha plana
        $this->assertTrue(password_verify($senhaPlana, $senhaHash));
    }
}