# Migração GeoFML Internet para PHP 8.4

Branch experimental, **não implantar em produção**.

## Alterações realizadas
- Mews CAPTCHA removido do composer.json e dos registros explícitos em config/app.php.
- Rota e método do CAPTCHA legado removidos.
- Google reCAPTCHA (App\\Rules\\ReCaptcha) preservado.
- Substituída dependência direta do Laratrust nos modelos, login indireto via HomeController e middleware de rotas.
- Modelo Role usa Eloquent; User expõe hasRole() e syncRoles() usando a tabela pivot existente.

## Validações obrigatórias
1. Confirmar em banco de homologação a existência de `roles`, `role_user`, `roles.name`, `role_user.user_id` e `role_user.role_id`.
2. Procurar usos residuais de `Laratrust`, `hasPermission`, `can`, `syncRoles`, `@role`, `captcha_img`, `captcha_src` e `/reload` em código e views.
3. Regenerar composer.lock em ambiente de homologação, sem --ignore-platform-reqs; **não usar composer install com o lock antigo**.
4. Atualizar Laravel 7 e pacotes legados para versões compatíveis com PHP 8.4 antes de executar Artisan no PHP 8.4.
5. Verificar login, autenticação, perfis, reservas, hospedagens, pagamentos, anexos e Google reCAPTCHA.
6. Confirmar que o Apache, não apenas o CLI, carrega PHP 8.4.

**A migração do Laravel e a regeneração do composer.lock ainda estão pendentes.**
