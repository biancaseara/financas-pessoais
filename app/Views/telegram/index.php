<div class="transactions-container">
    
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <i class="ph ph-check-circle" style="font-size: 18px;"></i> Conta vinculada com sucesso!
        </div>
    <?php endif; ?>

    <div class="card form-container">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 24px; display: block;">
            <h4><i class="ph-fill ph-telegram-logo" style="color: #0088cc; margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
            <p class="text-secondary" style="font-size: 0.9rem; margin-top: 12px; line-height: 1.5;">
                Registre suas despesas de forma rápida enviando uma mensagem no Telegram. Siga os passos abaixo para vincular sua conta.
            </p>
        </div>

        <div class="tutorial-steps" style="display: flex; flex-direction: column; gap: 24px;">
            
            <div class="step-card" style="display: flex; gap: 16px; background: var(--bg-main); padding: 20px; border-radius: 12px; border-left: 4px solid var(--color-ia-purple);">
                <div class="step-number" style="background: var(--color-ia-glow); color: var(--color-ia-purple); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
                    1
                </div>
                <div>
                    <h5 style="margin-bottom: 8px; font-size: 1.1rem; color: var(--text-primary);">Abra o nosso Bot Oficial</h5>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 12px;">
                        Abra o seu aplicativo do Telegram e busque pelo nosso bot oficial ou clique no botão abaixo para ir direto para a conversa.
                    </p>
                    <a href="https://t.me/preditivia_assist_bot" target="_blank" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: #0088cc; padding: 8px 16px; font-size: 0.9rem; text-decoration: none; border: none;">
                        <i class="ph-fill ph-telegram-logo"></i> Abrir Telegram
                    </a>
                </div>
            </div>

            <div class="step-card" style="display: flex; gap: 16px; background: var(--bg-main); padding: 20px; border-radius: 12px; border-left: 4px solid var(--color-ia-purple);">
                <div class="step-number" style="background: var(--color-ia-glow); color: var(--color-ia-purple); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
                    2
                </div>
                <div>
                    <h5 style="margin-bottom: 8px; font-size: 1.1rem; color: var(--text-primary);">Pegue o seu Código de Vínculo</h5>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">
                        Na conversa com o bot, clique em <strong>INICIAR</strong> ou digite <code>/start</code>. O bot vai te responder com um código de números (seu Chat ID). Copie esse número.
                    </p>
                </div>
            </div>

            <div class="step-card" style="display: flex; gap: 16px; background: var(--bg-main); padding: 20px; border-radius: 12px; border-left: 4px solid var(--color-ia-purple);">
                <div class="step-number" style="background: var(--color-ia-glow); color: var(--color-ia-purple); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
                    3
                </div>
                <div style="width: 100%;">
                    <h5 style="margin-bottom: 8px; font-size: 1.1rem; color: var(--text-primary);">Vincule à sua Conta</h5>
                    
                    <?php if (!empty($usuario['chat_id_telegram'])): ?>
                        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--color-emerald); padding: 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 12px;">
                            <div>
                                <strong style="color: var(--color-emerald); display: block; margin-bottom: 4px;"><i class="ph-fill ph-check-circle"></i> Conta Vinculada!</strong>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);">Chat ID: <?= htmlspecialchars($usuario['chat_id_telegram'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <form action="/financas/telegram/desvincular" method="POST" style="margin: 0;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn-outline danger" style="padding: 6px 12px; font-size: 0.9rem; border-color: #ef4444; color: #ef4444;" onclick="return confirm('Tem certeza que deseja desvincular o Telegram? Você não poderá mais registrar despesas por lá até vincular novamente.');">Desvincular</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 16px;">
                            Cole o código que o bot te enviou no campo abaixo e salve. Isso garante que as mensagens enviadas por lá caiam no seu perfil.
                        </p>
                        <form action="/financas/telegram/vincular" method="POST" style="display: flex; flex-wrap: wrap; gap: 12px; max-width: 400px;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="text" name="chat_id" class="form-control" placeholder="Ex: 123456789" required style="flex: 1; min-width: 200px;">
                            <button type="submit" class="btn-primary" style="white-space: nowrap;">Vincular Conta</button>
                        </form>
                    <?php endif; ?>
                    
                </div>
            </div>

            <div class="step-card" style="display: flex; gap: 16px; background: var(--bg-main); padding: 20px; border-radius: 12px; border-left: 4px solid var(--color-emerald);">
                <div class="step-number" style="background: rgba(16, 185, 129, 0.15); color: var(--color-emerald); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; flex-shrink: 0;">
                    <i class="ph ph-check"></i>
                </div>
                <div>
                    <h5 style="margin-bottom: 8px; font-size: 1.1rem; color: var(--text-primary);">Tudo Pronto! Comece a usar</h5>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">
                        Agora é só mandar uma mensagem natural pro bot, como: <br>
                        <strong style="color: var(--color-emerald);">"Gastei 50 reais de Uber no crédito"</strong> ou <strong style="color: var(--color-emerald);">"Comprei 120 de mercado no Pix"</strong>.<br>
                        O sistema vai ler, categorizar e lançar automaticamente!
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>