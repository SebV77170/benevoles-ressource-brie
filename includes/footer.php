    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script>
        $('#myModal').modal('show');
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const menuElement = document.getElementById('menuForum');
    const toggler = document.querySelector('.custom-toggler');

    if (!menuElement || !toggler) return;

    const bsCollapse = new bootstrap.Collapse(menuElement, {
        toggle: false
    });

    let inactivityTimer = null;
    const delay = 5000; // 5 secondes

    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);

        if (menuElement.classList.contains('show')) {
            inactivityTimer = setTimeout(() => {
                bsCollapse.hide();
            }, delay);
        }
    }

    function clearInactivityTimer() {
        clearTimeout(inactivityTimer);
    }

    menuElement.addEventListener('shown.bs.collapse', function () {
        resetInactivityTimer();
    });

    menuElement.addEventListener('hidden.bs.collapse', function () {
        clearInactivityTimer();
    });

    menuElement.addEventListener('mousemove', resetInactivityTimer);
    menuElement.addEventListener('click', resetInactivityTimer);
    menuElement.addEventListener('touchstart', resetInactivityTimer);
    menuElement.addEventListener('scroll', resetInactivityTimer);

    const menuLinks = menuElement.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function () {
            clearInactivityTimer();
            bsCollapse.hide();
        });
    });
});
</script>
    </body>
</html>
<footer class="footer">
    
    <p class="paraph_footer"> PGB (Programme Gestion Bénévoles) v1.0 Décembre 2022, réalisé par Sébastien VOILLOT pour Ressource'Brie</p>
    
</footer>