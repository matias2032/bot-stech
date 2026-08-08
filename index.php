<?php
// ============================================================
//  INDEX.PHP — Landing page do ChatBot de Educação Financeira
// ============================================================
require_once 'auth.php';
iniciarSessao();
$logado = estaLogado();
$nome   = $logado ? ($_SESSION['nome'] ?? 'Utilizador') : '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinBot — O teu assistente de educação financeira</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo.css">
    
</head>
<body>

<!-- ============================================================
     NAVBAR
============================================================ -->
<nav class="navbar" id="navbar">
    <a href="index.php" class="nav-logo">
        <div class="nav-logo-icone">
            <svg width="20" height="20" viewBox="0 0 28 28" fill="none">
                <circle cx="14" cy="14" r="13" stroke="var(--cor-acento)" stroke-width="1.5"/>
                <path d="M8 14c0-3.3 2.7-6 6-6s6 2.7 6 6-2.7 6-6 6" stroke="var(--cor-acento)" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="14" cy="14" r="2.5" fill="var(--cor-acento)"/>
            </svg>
        </div>
        <span class="nav-logo-nome">FinBot</span>
    </a>

    <div class="nav-links">
        <a href="#como-funciona" class="nav-link">Como funciona</a>
        <a href="#funcionalidades" class="nav-link">Funcionalidades</a>
        <a href="#topicos" class="nav-link">Tópicos</a>
    </div>

    <?php if ($logado): ?>
    <div class="nav-user">
        <div class="nav-user-avatar"><?= mb_strtoupper(mb_substr($nome, 0, 1)) ?></div>
        <span class="nav-user-nome"><?= htmlspecialchars($nome) ?></span>
        <a href="menu.php" class="nav-cta">Abrir Chat</a>
    </div>
    <?php else: ?>
    <div style="display:flex;gap:8px;align-items:center;">
        <a href="login.php" class="nav-link">Entrar</a>
        <a href="registo.php" class="nav-cta">Começar grátis</a>
    </div>
    <?php endif; ?>
</nav>


<!-- ============================================================
     HERO PRINCIPAL
============================================================ -->
<section class="hero">
    <div class="hero-bg">
        <div class="hero-grid"></div>
        <div class="hero-bg-glow hero-bg-glow-1"></div>
        <div class="hero-bg-glow hero-bg-glow-2"></div>
        <div class="hero-bg-glow hero-bg-glow-3"></div>
    </div>

    <div class="hero-badge">
        <div class="hero-badge-dot"></div>
        Assistente de IA activo e pronto a ajudar
    </div>

    <h1 class="hero-titulo">
        O teu guia pessoal para<br>
        <span class="hero-titulo-destaque">liberdade financeira</span>
    </h1>

    <p class="hero-subtitulo">
        Faz perguntas, aprende a investir, controla o orçamento e toma decisões financeiras mais inteligentes — tudo numa conversa natural.
    </p>

    <div class="hero-acoes">
        <?php if ($logado): ?>
            <a href="menu.php" class="btn-hero-primario">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Continuar conversa
            </a>
        <?php else: ?>
            <a href="registo.php" class="btn-hero-primario">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Começar agora — é grátis
            </a>
            <a href="menu.php" class="btn-hero-secundario">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" stroke="currentColor" stroke-width="2"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/></svg>
                Experimentar sem conta
            </a>
        <?php endif; ?>
    </div>

    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-valor"><span class="contador-animado" data-alvo="12">0</span>k+</div>
            <div class="hero-stat-label">Perguntas respondidas</div>
        </div>
        <div class="hero-stat-sep"></div>
        <div class="hero-stat">
            <div class="hero-stat-valor"><span class="contador-animado" data-alvo="98">0</span>%</div>
            <div class="hero-stat-label">Taxa de satisfação</div>
        </div>
        <div class="hero-stat-sep"></div>
        <div class="hero-stat">
            <div class="hero-stat-valor"><span class="contador-animado" data-alvo="24">0</span>/7</div>
            <div class="hero-stat-label">Disponível sempre</div>
        </div>
        <div class="hero-stat-sep"></div>
        <div class="hero-stat">
            <div class="hero-stat-valor"><span class="contador-animado" data-alvo="50">0</span>+</div>
            <div class="hero-stat-label">Tópicos financeiros</div>
        </div>
    </div>
</section>


<!-- ============================================================
     CARROSSEL DE HEROES
============================================================ -->
<section class="secao-carrossel">
    <p class="secao-label">Explora os temas</p>

    <div class="carrossel-pista" id="carrossel">

        <div class="hero-card">
            <img
                src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=80"
                alt="Investimentos"
                loading="lazy"
            >
            <div class="hero-card-overlay"></div>
            <div class="hero-card-conteudo">
                <span class="hero-card-tag tag-investimento">Investimentos</span>
                <h3 class="hero-card-titulo">Faz o teu dinheiro trabalhar por ti</h3>
                <p class="hero-card-desc">Aprende sobre acções, fundos e obrigações de forma simples.</p>
            </div>
        </div>

        <div class="hero-card">
            <img
                src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&q=80"
                alt="Poupança"
                loading="lazy"
            >
            <div class="hero-card-overlay"></div>
            <div class="hero-card-conteudo">
                <span class="hero-card-tag tag-poupanca">Poupança</span>
                <h3 class="hero-card-titulo">Constrói o teu fundo de emergência</h3>
                <p class="hero-card-desc">Estratégias práticas para poupar mais todos os meses.</p>
            </div>
        </div>

        <div class="hero-card">
            <img
                src="https://images.unsplash.com/photo-1434626881859-194d67b2b86f?w=800&q=80"
                alt="Orçamento"
                loading="lazy"
            >
            <div class="hero-card-overlay"></div>
            <div class="hero-card-conteudo">
                <span class="hero-card-tag tag-orcamento">Orçamento</span>
                <h3 class="hero-card-titulo">Controla as tuas finanças pessoais</h3>
                <p class="hero-card-desc">Métodos como 50/30/20 explicados passo a passo.</p>
            </div>
        </div>

        <div class="hero-card">
            <img
                src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&q=80"
                alt="Crédito"
                loading="lazy"
            >
            <div class="hero-card-overlay"></div>
            <div class="hero-card-conteudo">
                <span class="hero-card-tag tag-credito">Crédito</span>
                <h3 class="hero-card-titulo">Entende e melhora o teu score</h3>
                <p class="hero-card-desc">Como o crédito funciona e como usá-lo a teu favor.</p>
            </div>
        </div>

    </div>

    <div class="carrossel-dots" id="carrossel-dots">
        <div class="carrossel-dot ativo" data-idx="0"></div>
        <div class="carrossel-dot" data-idx="1"></div>
        <div class="carrossel-dot" data-idx="2"></div>
        <div class="carrossel-dot" data-idx="3"></div>
    </div>
</section>


<!-- ============================================================
     COMO FUNCIONA
============================================================ -->
<section class="secao" id="como-funciona">
    <div class="secao-cabecalho">
        <p class="secao-label">Como funciona</p>
        <h2 class="secao-titulo">Simples como uma conversa</h2>
        <p class="secao-subtitulo">Sem formulários complexos. Sem linguagem técnica. Apenas perguntas e respostas claras.</p>
    </div>

    <div class="passos">
        <div class="passo">
            <div class="passo-numero">01</div>
            <div class="passo-icone" style="background:rgba(108,143,255,0.12);">💬</div>
            <div class="passo-titulo">Faz a tua pergunta</div>
            <div class="passo-desc">Escreve o que quiseres saber sobre finanças, desde o básico ao avançado, sem julgamentos.</div>
            <div class="passo-linha"></div>
        </div>
        <div class="passo">
            <div class="passo-numero">02</div>
            <div class="passo-icone" style="background:rgba(74,222,128,0.1);">🧠</div>
            <div class="passo-titulo">O FinBot analisa</div>
            <div class="passo-desc">A IA processa a tua questão com base em conhecimento financeiro actualizado e contextualizado para a tua situação.</div>
            <div class="passo-linha"></div>
        </div>
        <div class="passo">
            <div class="passo-numero">03</div>
            <div class="passo-icone" style="background:rgba(251,191,36,0.1);">✨</div>
            <div class="passo-titulo">Recebe orientação clara</div>
            <div class="passo-desc">Obtens uma resposta personalizada, com exemplos práticos e sugestões de próximos passos.</div>
        </div>
    </div>
</section>


<!-- ============================================================
     DEMO DE CHAT
============================================================ -->
<section class="secao-demo">
    <div class="demo-inner">
        <div class="demo-texto">
            <div class="demo-badge">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"><circle cx="5" cy="5" r="5"/></svg>
                Conversa em tempo real
            </div>
            <h2 class="demo-titulo">Respostas que realmente ajudam</h2>
            <p class="demo-desc">
                O FinBot não te dá respostas genéricas. Entende o teu contexto e responde de forma directa e útil, como um consultor financeiro acessível.
            </p>
            <ul class="demo-lista">
                <li>Explica conceitos complexos de forma simples</li>
                <li>Adapta-se ao teu nível de conhecimento</li>
                <li>Sugere recursos e próximos passos concretos</li>
                <li>Disponível 24 horas, 7 dias por semana</li>
            </ul>
        </div>

        <div class="chat-mockup">
            <div class="chat-mockup-header">
                <div class="chat-mockup-dot"></div>
                <span class="chat-mockup-nome">FinBot — Online</span>
            </div>
            <div class="chat-mockup-body" id="chat-demo">
                <div class="chat-msg chat-msg-bot" id="msg1">
                    <div class="chat-msg-avatar">🤖</div>
                    <div class="chat-msg-balao">Olá! Como posso ajudar-te hoje com as tuas finanças?</div>
                </div>
                <div class="chat-msg chat-msg-user" id="msg2">
                    <div class="chat-msg-avatar">👤</div>
                    <div class="chat-msg-balao">O que é um fundo de emergência e quanto devo ter?</div>
                </div>
                <div class="chat-msg chat-msg-bot" id="msg3">
                    <div class="chat-msg-avatar">🤖</div>
                    <div class="chat-msg-balao">Um fundo de emergência é uma reserva para imprevistos. O ideal é ter <strong style="color:var(--cor-acento)">3 a 6 meses</strong> de despesas guardados numa conta de fácil acesso. Quer que te explique como calcular o valor certo para o teu caso?</div>
                </div>
                <div class="chat-msg chat-msg-user" id="msg4">
                    <div class="chat-msg-avatar">👤</div>
                    <div class="chat-msg-balao">Sim, por favor!</div>
                </div>
            </div>
            <div class="chat-mockup-input">
                <div class="chat-mockup-input-field">Faz uma pergunta…</div>
                <button class="chat-mockup-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     FUNCIONALIDADES
============================================================ -->
<section class="secao" id="funcionalidades">
    <div class="secao-cabecalho">
        <p class="secao-label">Funcionalidades</p>
        <h2 class="secao-titulo">Tudo o que precisas para evoluir</h2>
        <p class="secao-subtitulo">Uma plataforma completa para a tua educação financeira.</p>
    </div>

    <div class="funcionalidades">
        <div class="func-card">
            <div class="func-icone" style="background:rgba(108,143,255,0.12);">📊</div>
            <div class="func-texto">
                <div class="func-titulo">Análise de orçamento</div>
                <div class="func-desc">Descreve as tuas receitas e despesas e recebe sugestões personalizadas para optimizar as tuas finanças.</div>
            </div>
        </div>
        <div class="func-card">
            <div class="func-icone" style="background:rgba(74,222,128,0.1);">📈</div>
            <div class="func-texto">
                <div class="func-titulo">Guia de investimentos</div>
                <div class="func-desc">Aprende sobre acções, ETFs, fundos de investimento, criptomoedas e muito mais, com linguagem acessível.</div>
            </div>
        </div>
        <div class="func-card">
            <div class="func-icone" style="background:rgba(251,191,36,0.1);">🎯</div>
            <div class="func-texto">
                <div class="func-titulo">Objectivos financeiros</div>
                <div class="func-desc">Define metas como comprar casa, reformar cedo ou pagar dívidas, e recebe um plano de acção estruturado.</div>
            </div>
        </div>
        <div class="func-card">
            <div class="func-icone" style="background:rgba(96,165,250,0.1);">🔐</div>
            <div class="func-texto">
                <div class="func-titulo">Histórico de conversas</div>
                <div class="func-desc">Com conta criada, o teu histórico fica guardado para poderes retomar onde ficaste ou revisitar conselhos anteriores.</div>
            </div>
        </div>
        <div class="func-card">
            <div class="func-icone" style="background:rgba(167,139,250,0.1);">📚</div>
            <div class="func-texto">
                <div class="func-titulo">Base de conhecimento</div>
                <div class="func-desc">Respaldado por uma base de dados financeira constantemente actualizada com conceitos, estratégias e exemplos práticos.</div>
            </div>
        </div>
        <div class="func-card">
            <div class="func-icone" style="background:rgba(248,113,113,0.1);">⚡</div>
            <div class="func-texto">
                <div class="func-titulo">Respostas instantâneas</div>
                <div class="func-desc">Sem esperas. Recebe respostas detalhadas em segundos, disponível a qualquer hora do dia ou da noite.</div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     TÓPICOS INTERACTIVOS
============================================================ -->
<section class="secao-topicos" id="topicos">
    <div class="secao-cabecalho">
        <p class="secao-label">Explora tópicos</p>
        <h2 class="secao-titulo">O que queres aprender hoje?</h2>
        <p class="secao-subtitulo">Clica num tópico para começar uma conversa directamente sobre esse tema.</p>
    </div>

    <div class="topicos-grid">
        <button class="topico-chip" onclick="abrirTopico('O que são juros compostos e como funcionam?')">Juros compostos</button>
        <button class="topico-chip" onclick="abrirTopico('O que é Poupança e Xitique e como posso usá-los?')">Poupança/Xitique</button>
        <button class="topico-chip" onclick="abrirTopico('Como criar um orçamento familiar eficiente?')">Orçamento familiar</button>
        <button class="topico-chip" onclick="abrirTopico('Explica-me a regra 50/30/20 para gerir o meu dinheiro.')">Regra 50/30/20</button>
        <button class="topico-chip" onclick="abrirTopico('O que é a inflação e como me afecta?')">Inflação</button>
        <button class="topico-chip" onclick="abrirTopico('Como posso sair das dívidas de forma eficaz?')">Sair das dívidas</button>
        <button class="topico-chip" onclick="abrirTopico('O que são acções e como investir nelas?')">Acções</button>
        <button class="topico-chip" onclick="abrirTopico('Como calcular e planear uma reforma antecipada?')">Reforma antecipada</button>
        <button class="topico-chip" onclick="abrirTopico('O que é o score de crédito e como melhorá-lo?')">Score de crédito</button>
        <button class="topico-chip" onclick="abrirTopico('Como posso investir com pouco dinheiro?')"><span>💰</span> Investir com pouco</button>
        <button class="topico-chip" onclick="abrirTopico('O que é diversificação de investimentos e por que é importante?')"><span>🌐</span> Diversificação</button>
        <button class="topico-chip" onclick="abrirTopico('O que é um PPR e como funciona para a reforma?')"><span>🛡️</span> PPR / Reforma</button>
        <button class="topico-chip" onclick="abrirTopico('O que são criptomoedas e como investir com segurança?')"><span>🔐</span> Criptomoedas</button>
    </div>
</section>


<!-- ============================================================
     TESTEMUNHOS
============================================================ -->
<section class="secao-testemunhos">
    <div class="testemunhos-inner">
        <div class="secao-cabecalho">
            <p class="secao-label">Testemunhos</p>
            <h2 class="secao-titulo">O que dizem os utilizadores</h2>
            <p class="secao-subtitulo">Pessoas reais que melhoraram a sua relação com o dinheiro.</p>
        </div>
        <div class="testemunhos-grid">
            <div class="testemunho">
                <div class="testemunho-estrelas">★★★★★</div>
                <p class="testemunho-texto">"Finalmente percebi o que são juros compostos e como usá-los a meu favor. O FinBot explica tudo sem linguagem chata."</p>
                <div class="testemunho-autor">
                    <div class="testemunho-avatar">MA</div>
                    <div>
                        <div class="testemunho-nome">Maria A.</div>
                        <div class="testemunho-cargo">Professora</div>
                    </div>
                </div>
            </div>
            <div class="testemunho">
                <div class="testemunho-estrelas">★★★★★</div>
                <p class="testemunho-texto">"Consegui criar o meu primeiro orçamento a sério depois de perguntar ao FinBot como aplicar a regra 50/30/20 ao meu salário."</p>
                <div class="testemunho-autor">
                    <div class="testemunho-avatar">JC</div>
                    <div>
                        <div class="testemunho-nome">João C.</div>
                        <div class="testemunho-cargo">Engenheiro</div>
                    </div>
                </div>
            </div>
            <div class="testemunho">
                <div class="testemunho-estrelas">★★★★☆</div>
                <p class="testemunho-texto">"Uso quase todos os dias para tirar dúvidas sobre investimentos. É como ter um consultor financeiro sempre disponível."</p>
                <div class="testemunho-autor">
                    <div class="testemunho-avatar">SP</div>
                    <div>
                        <div class="testemunho-nome">Sofia P.</div>
                        <div class="testemunho-cargo">Empreendedora</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     CTA FINAL
============================================================ -->
<section class="secao-cta">
    <div class="cta-bg"><div class="cta-glow"></div></div>
    <h2 class="cta-titulo">Pronto para transformar as tuas finanças?</h2>
    <p class="cta-desc">Junta-te a milhares de pessoas que já usam o FinBot para tomar melhores decisões financeiras todos os dias.</p>
    <div class="cta-acoes">
        <?php if ($logado): ?>
            <a href="menu.php" class="btn-hero-primario">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Abrir o Chat
            </a>
        <?php else: ?>
            <a href="registo.php" class="btn-hero-primario">
                Criar conta gratuita
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <a href="menu.php" class="btn-hero-secundario">Experimentar sem conta</a>
        <?php endif; ?>
    </div>
</section>


<!-- ============================================================
     RODAPÉ
============================================================ -->
<footer class="rodape">
    <div class="rodape-logo">
        <div class="nav-logo-icone" style="width:28px;height:28px;">
            <svg width="16" height="16" viewBox="0 0 28 28" fill="none">
                <circle cx="14" cy="14" r="13" stroke="var(--cor-acento)" stroke-width="1.5"/>
                <circle cx="14" cy="14" r="2.5" fill="var(--cor-acento)"/>
            </svg>
        </div>
        <span class="rodape-nome">FinBot</span>
    </div>
    <span class="rodape-copy">© <?= date('Y') ?> FinBot — Educação Financeira com IA</span>
    <div class="rodape-links">
        <a href="login.php" class="rodape-link">Entrar</a>
        <a href="registo.php" class="rodape-link">Registar</a>
        <a href="menu.php" class="rodape-link">Chat</a>
    </div>
</footer>


<!-- ============================================================
     JAVASCRIPT
============================================================ -->
<script>
(function() {

    /* ── Navbar scroll ── */
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });


    /* ── Smooth scroll para âncoras ── */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const alvo = document.querySelector(a.getAttribute('href'));
            if (alvo) {
                e.preventDefault();
                alvo.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });


    /* ── Carrossel (drag + dots) ── */
    const pista   = document.getElementById('carrossel');
    const dots    = document.querySelectorAll('.carrossel-dot');
    let arrastando = false, startX = 0, scrollStart = 0;

    pista.addEventListener('mousedown', e => {
        arrastando = true;
        startX = e.pageX - pista.offsetLeft;
        scrollStart = pista.scrollLeft;
        pista.classList.add('arrastando');
    });
    window.addEventListener('mousemove', e => {
        if (!arrastando) return;
        const x = e.pageX - pista.offsetLeft;
        pista.scrollLeft = scrollStart - (x - startX);
    });
    window.addEventListener('mouseup', () => {
        arrastando = false;
        pista.classList.remove('arrastando');
    });

    /* Touch */
    pista.addEventListener('touchstart', e => {
        startX = e.touches[0].pageX;
        scrollStart = pista.scrollLeft;
    }, { passive: true });
    pista.addEventListener('touchmove', e => {
        const x = e.touches[0].pageX;
        pista.scrollLeft = scrollStart - (x - startX);
    }, { passive: true });

    /* Dots */
    pista.addEventListener('scroll', () => {
        const largura = pista.querySelector('.hero-card').offsetWidth + 20;
        const idx = Math.round(pista.scrollLeft / largura);
        dots.forEach((d, i) => d.classList.toggle('ativo', i === idx));
    }, { passive: true });

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const largura = pista.querySelector('.hero-card').offsetWidth + 20;
            pista.scrollTo({ left: parseInt(dot.dataset.idx) * largura, behavior: 'smooth' });
        });
    });


    /* ── Contador animado ── */
    function animarContador(el) {
        const alvo = parseInt(el.dataset.alvo);
        const duracao = 1800;
        const inicio = performance.now();
        const step = ts => {
            const prog = Math.min((ts - inicio) / duracao, 1);
            const ease = 1 - Math.pow(1 - prog, 3);
            el.textContent = Math.floor(ease * alvo);
            if (prog < 1) requestAnimationFrame(step);
            else el.textContent = alvo;
        };
        requestAnimationFrame(step);
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                animarContador(e.target);
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.contador-animado').forEach(el => observer.observe(el));


    /* ── Demo de chat animado ── */
    const msgs = ['msg1','msg2','msg3','msg4'];
    const demoObserver = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            msgs.forEach((id, i) => {
                setTimeout(() => {
                    const el = document.getElementById(id);
                    if (el) el.classList.add('visivel');
                }, i * 600);
            });
            demoObserver.disconnect();
        }
    }, { threshold: 0.3 });

    const demoEl = document.getElementById('chat-demo');
    if (demoEl) demoObserver.observe(demoEl);


    /* ── Fade-in ao scroll (passos, cards, testemunhos) ── */
    const fadeObserver = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.opacity = '1';
                e.target.style.transform = 'translateY(0)';
                fadeObserver.unobserve(e.target);
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.passo, .func-card, .testemunho, .topico-chip').forEach((el, i) => {
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(24px)';
        el.style.transition = `opacity 0.5s ease ${i * 0.06}s, transform 0.5s ease ${i * 0.06}s`;
        fadeObserver.observe(el);
    });


})();

        /* ── Tópicos: guardar no localStorage e ir para o chat ── */
function abrirTopico(pergunta) {
    window.location.href = 'menu.php?topico=' + encodeURIComponent(pergunta);
}

</script>

</body>
</html>