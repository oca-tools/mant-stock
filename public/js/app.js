// Scripts gerais do sistema
(function () {
    const filtros = document.querySelectorAll('.js-filtrar-select');
    filtros.forEach(function (input) {
        const alvo = input.getAttribute('data-alvo');
        const select = document.querySelector('select[name="' + alvo + '"]');
        if (!select) return;

        const opcoesOriginais = Array.from(select.options).map(function (opt) {
            return { value: opt.value, text: opt.text };
        });

        input.addEventListener('input', function () {
            const termo = input.value.toLowerCase();
            select.innerHTML = '';
            opcoesOriginais.forEach(function (op) {
                if (op.value === '' || op.text.toLowerCase().includes(termo)) {
                    const option = document.createElement('option');
                    option.value = op.value;
                    option.textContent = op.text;
                    select.appendChild(option);
                }
            });
        });
    });

    const relogio = document.getElementById('relogio-topo');
    if (relogio) {
        const atualizar = function () {
            const agora = new Date();
            relogio.textContent = agora.toLocaleString('pt-BR', {
                dateStyle: 'short',
                timeStyle: 'medium'
            });
        };
        atualizar();
        setInterval(atualizar, 1000);
    }

    const botaoTema = document.getElementById('toggle-theme');
    if (botaoTema) {
        const aplicarTema = function (tema) {
            document.documentElement.setAttribute('data-theme', tema);
            localStorage.setItem('tema', tema);
        };
        const temaSalvo = localStorage.getItem('tema') || 'light';
        aplicarTema(temaSalvo);

        botaoTema.addEventListener('click', function () {
            const atual = document.documentElement.getAttribute('data-theme');
            aplicarTema(atual === 'dark' ? 'light' : 'dark');
        });
    }

    const botaoSidebar = document.getElementById('toggle-sidebar');
    if (botaoSidebar) {
        botaoSidebar.addEventListener('click', function () {
            document.querySelector('.app-shell').classList.toggle('sidebar-open');
        });
    }

    const collapses = document.querySelectorAll('.app-sidebar .collapse');
    collapses.forEach(function (el) {
        const chave = 'menu_' + el.id;
        const salvo = localStorage.getItem(chave);
        if (salvo === 'hide') {
            el.classList.remove('show');
        }
        el.addEventListener('shown.bs.collapse', function () {
            localStorage.setItem(chave, 'show');
        });
        el.addEventListener('hidden.bs.collapse', function () {
            localStorage.setItem(chave, 'hide');
        });
    });
})();
