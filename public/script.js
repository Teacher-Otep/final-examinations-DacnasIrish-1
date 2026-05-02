var SECTIONS = ['home', 'create', 'read', 'update', 'delete'];

function showSection(id) {
    SECTIONS.forEach(function (sec) {
        var el = document.getElementById(sec);
        if (!el) return;
        if (sec === id) {
            el.classList.remove('hidden');
            el.classList.add('active');
        } else {
            el.classList.add('hidden');
            el.classList.remove('active');
        }
    });

    document.querySelectorAll('.nav-btn').forEach(function (btn) {
        if (btn.getAttribute('data-section') === id) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

function clearFields() {
    var form = document.getElementById('create-form');
    if (!form) return;
    form.querySelectorAll('input[type="text"], input[type="number"]').forEach(function (inp) {
        inp.value = '';
    });
    form.querySelectorAll('textarea').forEach(function (ta) {
        ta.value = '';
    });
}

document.addEventListener('DOMContentLoaded', function () {

    /* Nav buttons: instant switch, no URL change (so refresh goes Home) */
    document.querySelectorAll('.nav-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var sec = btn.getAttribute('data-section');
            if (sec) showSection(sec);
        });
    });

    /* Logo click → Home */
    var logo = document.getElementById('logo');
    if (logo) {
        logo.addEventListener('click', function () {
            showSection('home');
        });
    }

    /* Auto-dismiss flash alert */
    var flash = document.getElementById('flash-msg');
    if (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity    = '0';
            setTimeout(function () { flash.remove(); }, 500);
        }, 4000);
    }

    /* Custom Modal Handling */
    var modal = document.getElementById('confirm-modal');
    var btnDelete = document.getElementById('btn-confirm-delete');
    var btnCancel = document.getElementById('modal-cancel');
    var btnConfirm = document.getElementById('modal-confirm');
    var deleteForm = document.getElementById('delete-form');

    if (btnDelete && modal && deleteForm) {
        btnDelete.addEventListener('click', function () {
            if (deleteForm.checkValidity()) {
                modal.classList.add('open');
            } else {
                deleteForm.reportValidity();
            }
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', function () {
            modal.classList.remove('open');
        });
    }

    if (btnConfirm && deleteForm) {
        btnConfirm.addEventListener('click', function () {
            deleteForm.submit();
        });
    }

    /* Close modal on overlay click */
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.classList.remove('open');
        });
    }
});
