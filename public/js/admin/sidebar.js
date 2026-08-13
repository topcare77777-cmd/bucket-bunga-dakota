export const sidebar = {
    init() {
        const btn = document.querySelector('.toggle-sidebar');
        btn?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
        });
    }
};

sidebar.init();