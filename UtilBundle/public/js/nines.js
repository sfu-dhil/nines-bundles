(function ($, window, document) {

    const hostname = window.location.hostname.replace('www.', '');

    function confirm() {
        const $this = $(this);
        $this.click(function () {
            return window.confirm($this.data('confirm'));
        });
    }

    function link() {
        if(this.hostname.replace('www.', '') === hostname) {
            return;
        }
        $(this).attr('target', '_blank');
    }

    function windowBeforeUnload(e) {
        let clean = true;
        $('form').each(function () {
            const $form = $(this);
            if ($form.data('dirty')) {
                clean = false;
            }
        });
        if (!clean) {
            let message = 'You have unsaved changes.';
            e.returnValue = message;
            return message;
        }
    }

    function formDirty() {
        const $form = $(this);
        $form.data('dirty', false);
        $form.on('change', function () {
            $form.data('dirty', true);
        });
        $form.on('submit', function () {
            $(window).unbind('beforeunload');
        });
    }

    function simpleCollection() {
        if ( $('.collection-simple').length == 0 ) {
            return
        }
        $('.collection-simple').collection({
            init_with_n_elements: 1,
            allow_up: false,
            allow_down: false,
            preserve_names: true,
            max: 400,
            add: '<a href="#" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle" aria-hidden="true"></i></a> <span class="visually-hidden">Add</span>',
            remove: '<a href="#" class="btn btn-primary btn-sm"><i class="bi bi-dash-circle" aria-hidden="true"></i> <span class="visually-hidden">Remove</span></a>',
        });
    }

    function complexCollection() {
        if ( $('.collection-complex').length == 0 ) {
            return
        }
        $('.collection-complex').each( (_, collectionEl) => {
            const label = $(collectionEl).data('collection-label');
            const prompt = label ? `Add New ${label}` : `Add New Item`;
            $(collectionEl).collection({
                init_with_n_elements: 0,
                allow_up: false,
                allow_down: false,
                max: 400,
                position_field_selector: '.position-id',
                add_at_the_end: true,
                add: `<a href="#" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle" aria-hidden="true"></i> ${prompt}</a>`,
                remove: '<a href="#" class="btn btn-primary btn-sm"><i class="bi bi-dash-circle" aria-hidden="true"></i> <span class="visually-hidden">Remove</span></a>',
                after_add: function(_, element){
                    $(element).find('.select2entity').select2entity();
                    $(element).find('.select2-simple').select2({
                        width: '100%',
                    });
                    if (tinymce && $(element).find('.tinymce').length > 0 ) {
                        $(element).find('.tinymce').each(function (_, textarea) {
                            tinymce.execCommand("mceAddEditor", false, textarea.id);
                        });
                    }
                    return true;
                },
            })
        });
    }

    function imageModals() {
        $('#imgModal').on('show.bs.modal', function (event) {
            const $button = $(event.relatedTarget);
            // Button that triggered the modal
            const $modal = $(this);

            $modal.find('#modalTitle').text($button.data('title'));
            $modal.find('figcaption').html($button.parent().parent().find('.caption').clone());
            $modal.find("#modalImage").attr('src', $button.data('img'));
        });
    }

    function menuTabs() {
        const localStorageId = `tab-${location.href}-target`
        const lastShownTab = localStorage.getItem(localStorageId)

        const tabToggleList = document.querySelectorAll('[data-bs-toggle="tab"]')
        tabToggleList.forEach(function (tabToggle) {
            tabToggle.addEventListener('shown.bs.tab', () => {
                localStorage.setItem(localStorageId, tabToggle.id)
            })
            if (lastShownTab === tabToggle.id) {
                new bootstrap.Tab(tabToggle).show()
            }
        });
    }

    $(document).ready(function () {
        $(window).bind('beforeunload', windowBeforeUnload);
        $('form').each(formDirty);
        $("a").each(link);
        $("*[data-confirm]").each(confirm);
        let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                html: true,
                trigger: 'focus',
            })
        }) // add this line to enable bootstrap popover
        let alertList = document.querySelectorAll('.alert')
        alertList.forEach(function (alert) {
            new bootstrap.Alert(alert);
        }); // add alert dismissal
        if (typeof $().collection === 'function') {
            simpleCollection();
            complexCollection();
        }
        imageModals();
        menuTabs();
        $('.select2-simple').select2({
            width: '100%',
        });

        const select2entityModal = document.getElementById('select2entity_modal')
        if (select2entityModal) {
            select2entityModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget
                const contentRoute = button.getAttribute('data-modal-content-route');
                const resultTarget = button.getAttribute('data-modal-result-target');
                $(select2entityModal).find('.modal-content').load(contentRoute, () => {
                    // form submission
                    $(select2entityModal).find('form').submit((formSubmitEvent) => {
                        formSubmitEvent.preventDefault();
                        $.post(
                            $(select2entityModal).find('form').attr("action"),
                            $(select2entityModal).find('form').serialize(),
                            (result) => {
                                if (result.success) {
                                    if (resultTarget && result.data) {
                                        const { id, text } = result.data;
                                        const newOption = new Option(text, id, true, true);
                                        $(resultTarget).append(newOption).val(id).trigger('change');
                                    }
                                    $(select2entityModal).modal('hide');
                                } else {
                                    alert(`There was a problem saving the form: \n${result.errors.join('\n')}`);
                                }
                            },
                        );
                    });
                    // initialize select2entity, select2 simple, and tinymce for modal content
                    $(select2entityModal).find('.select2entity').select2entity();
                    $(select2entityModal).find('.select2-simple').select2({
                        width: '100%',
                    });
                    if (tinymce && $(select2entityModal).find('.tinymce').length > 0 ) {
                        $(select2entityModal).find('.tinymce').each(function (index, textarea) {
                            tinymce.execCommand('mceRemoveEditor', false, textarea.id);
                            tinymce.execCommand("mceAddEditor", false, textarea.id);
                        });
                    }
                });
            });
            select2entityModal.addEventListener('hidden.bs.modal', () => {
                // remove current modal content on hide
                $(select2entityModal).find('.modal-content').html('');
            });
        }
    });

})(jQuery, window, document);
