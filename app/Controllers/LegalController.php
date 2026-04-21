<?php 

class LegalController extends Controller {
    public function termos() {
        $this->view('legal/termos', [
            'titulo' => 'Termos de Uso'
        ]);
    }

    public function privacidade() {
        $this->view('legal/privacidade', [
            'titulo' => 'Política de Privacidade'
        ]);
    }

}
?>