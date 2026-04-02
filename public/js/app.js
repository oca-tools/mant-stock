// Scripts gerais de interface do sistema
(function () {
    var raiz = document.documentElement;
    var corpo = document.body;
    var shell = document.getElementById('app-shell');

    function selecionar(consulta, contexto) {
        return (contexto || document).querySelector(consulta);
    }

    function selecionarTodos(consulta, contexto) {
        return Array.from((contexto || document).querySelectorAll(consulta));
    }

    function aplicarTema(tema) {
        var temaFinal = tema === 'dark' ? 'dark' : 'light';
        raiz.setAttribute('data-theme', temaFinal);
        localStorage.setItem('tema_ui', temaFinal);

        var botaoTema = selecionar('#toggle-theme');
        if (!botaoTema) return;
        botaoTema.innerHTML = temaFinal === 'dark'
            ? '<i class="bi bi-sun"></i>'
            : '<i class="bi bi-moon-stars"></i>';
    }

    function iniciarTema() {
        var temaSalvo = localStorage.getItem('tema_ui');
        var prefereEscuro = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var temaInicial = temaSalvo || (prefereEscuro ? 'dark' : 'light');
        aplicarTema(temaInicial);

        var botaoTema = selecionar('#toggle-theme');
        if (botaoTema) {
            botaoTema.addEventListener('click', function () {
                var atual = raiz.getAttribute('data-theme') || 'light';
                aplicarTema(atual === 'dark' ? 'light' : 'dark');
            });
        }
    }

    function iniciarRelogio() {
        var relogio = selecionar('#relogio-topo');
        if (!relogio) return;
        var opcoes = {
            dateStyle: 'short',
            timeStyle: 'medium'
        };
        function atualizar() {
            relogio.textContent = new Date().toLocaleString('pt-BR', opcoes);
        }
        atualizar();
        setInterval(atualizar, 1000);
    }

    function abrirSidebar() {
        if (!shell) return;
        shell.classList.add('sidebar-open');
        corpo.style.overflow = 'hidden';
    }

    function fecharSidebar() {
        if (!shell) return;
        shell.classList.remove('sidebar-open');
        corpo.style.overflow = '';
    }

    function iniciarSidebar() {
        if (!shell) return;

        var botaoAbrir = selecionar('#toggle-sidebar');
        var botaoFechar = selecionar('#close-sidebar');
        var overlay = selecionar('#sidebar-overlay');

        if (botaoAbrir) {
            botaoAbrir.addEventListener('click', abrirSidebar);
        }
        if (botaoFechar) {
            botaoFechar.addEventListener('click', fecharSidebar);
        }
        if (overlay) {
            overlay.addEventListener('click', fecharSidebar);
        }

        selecionarTodos('.sidebar-link, .sidebar-sublink', selecionar('#app-sidebar')).forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 1200) {
                    fecharSidebar();
                }
            });
        });

        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') {
                fecharSidebar();
            }
        });

        if (window.matchMedia) {
            var queryDesktop = window.matchMedia('(min-width: 1200px)');
            queryDesktop.addEventListener('change', function (evento) {
                if (evento.matches) {
                    fecharSidebar();
                }
            });
        }
    }

    function chaveMenu(el) {
        return 'menu_estado_' + (el.getAttribute('data-menu-chave') || el.id || 'sem_id');
    }

    function iniciarPersistenciaMenus() {
        selecionarTodos('.app-sidebar .collapse').forEach(function (el) {
            var salvo = localStorage.getItem(chaveMenu(el));
            if (salvo === 'hide') {
                el.classList.remove('show');
            } else if (salvo === 'show') {
                el.classList.add('show');
            }

            el.addEventListener('shown.bs.collapse', function () {
                localStorage.setItem(chaveMenu(el), 'show');
            });
            el.addEventListener('hidden.bs.collapse', function () {
                localStorage.setItem(chaveMenu(el), 'hide');
            });
        });
    }

    function iniciarFiltroSelect() {
        selecionarTodos('.js-filtrar-select').forEach(function (input) {
            var alvo = input.getAttribute('data-alvo');
            var select = selecionar('select[name="' + alvo + '"]');
            if (!select) return;

            var opcoesOriginais = Array.from(select.options).map(function (opt) {
                return { value: opt.value, text: opt.text };
            });

            input.addEventListener('input', function () {
                var termo = input.value.toLowerCase();
                select.innerHTML = '';
                opcoesOriginais.forEach(function (op) {
                    if (op.value === '' || op.text.toLowerCase().indexOf(termo) >= 0) {
                        var option = document.createElement('option');
                        option.value = op.value;
                        option.textContent = op.text;
                        select.appendChild(option);
                    }
                });
            });
        });
    }

    function iniciarVisualizacaoEstoque() {
        var bloco = selecionar('[data-estoque-bloco]');
        if (!bloco) return;

        var botoes = selecionarTodos('[data-estoque-view]', bloco);
        var views = selecionarTodos('[data-estoque-content]', bloco);
        if (!botoes.length || !views.length) return;

        var chave = 'estoque_visualizacao';

        function aplicarVisualizacao(modo) {
            var modoFinal = modo === 'tabela' ? 'tabela' : 'lista';
            localStorage.setItem(chave, modoFinal);

            botoes.forEach(function (botao) {
                var ativo = botao.getAttribute('data-estoque-view') === modoFinal;
                botao.classList.toggle('btn-primary', ativo);
                botao.classList.toggle('btn-outline-primary', !ativo);
                botao.setAttribute('aria-pressed', ativo ? 'true' : 'false');
            });

            views.forEach(function (view) {
                var visivel = view.getAttribute('data-estoque-content') === modoFinal;
                view.classList.toggle('d-none', !visivel);
            });
        }

        var inicial = localStorage.getItem(chave) || 'lista';
        aplicarVisualizacao(inicial);

        botoes.forEach(function (botao) {
            botao.addEventListener('click', function () {
                aplicarVisualizacao(botao.getAttribute('data-estoque-view'));
            });
        });
    }

    function iniciarValidacaoFormularios() {
        selecionarTodos('form').forEach(function (formulario) {
            if ((formulario.method || '').toUpperCase() !== 'POST') return;
            formulario.addEventListener('submit', function (evento) {
                if (!formulario.checkValidity()) {
                    evento.preventDefault();
                    evento.stopPropagation();
                }
                formulario.classList.add('was-validated');
            });
        });
    }

    function iniciarAlertas() {
        selecionarTodos('.alert').forEach(function (alerta) {
            if (!alerta.classList.contains('alert-success')) return;
            setTimeout(function () {
                alerta.style.transition = 'opacity .25s ease, transform .25s ease';
                alerta.style.opacity = '0';
                alerta.style.transform = 'translateY(-4px)';
                setTimeout(function () {
                    if (alerta.parentNode) {
                        alerta.parentNode.removeChild(alerta);
                    }
                }, 260);
            }, 4200);
        });
    }

    iniciarTema();
    iniciarRelogio();
    iniciarSidebar();
    iniciarPersistenciaMenus();
    iniciarFiltroSelect();
    iniciarVisualizacaoEstoque();
    iniciarValidacaoFormularios();
    iniciarAlertas();
})();
