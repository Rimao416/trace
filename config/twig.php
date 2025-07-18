$twig = new \Twig\Environment($loader, [
    'sandbox' => true,
    'strict_variables' => true,
    'autoescape' => 'html'
]);

// Configurar o sandbox
$policy = new \Twig\Sandbox\SecurityPolicy(
    ['escape'], // filtros permitidos
    [], // tags permitidas
    [], // propriedades permitidas
    [], // métodos permitidos
    [] // funções permitidas
);
$sandbox = new \Twig\Sandbox\SecurityPolicyChecker($policy);
$twig->addExtension(new \Twig\Extension\SandboxExtension($sandbox, true)); 