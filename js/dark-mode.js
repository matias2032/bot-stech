/* ============================================================
   DARK-MODE.JS — Alternância de tema, partilhado por todas as páginas
   Aplica o tema o mais cedo possível para evitar "flash" de tema claro
============================================================ */

(function () {
    const CHAVE_STORAGE = 'tema-preferido'; // valores: 'escuro' | 'claro'

    function obterTemaGuardado() {
        return localStorage.getItem(CHAVE_STORAGE);
    }

    function aplicarTema(tema) {
        if (tema === 'escuro') {
            document.documentElement.setAttribute('data-tema', 'escuro');
        } else {
            document.documentElement.removeAttribute('data-tema');
        }
    }

    // Aplica imediatamente (chamado via script inline no <head>, ver abaixo)
    const temaGuardado = obterTemaGuardado();
    aplicarTema(temaGuardado);

    // Expor função de alternância global, ligada ao botão em cada página
    window.alternarTema = function () {
        const temaAtual = document.documentElement.getAttribute('data-tema') === 'escuro' ? 'escuro' : 'claro';
        const novoTema = temaAtual === 'escuro' ? 'claro' : 'escuro';
        aplicarTema(novoTema);
        localStorage.setItem(CHAVE_STORAGE, novoTema);
    };

    // Liga o botão assim que o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('btn-tema');
        if (btn) {
            btn.addEventListener('click', window.alternarTema);
        }
    });
})();