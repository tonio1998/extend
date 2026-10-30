@props([
    'name'           => 'user_id',
    'id'             => null,
    'label'          => null,
    'placeholder'    => '— Select User —',
    'value'          => null,       // pre-selected user ID (single) or array of IDs (multiple)
    'multiple'       => false,
    'required'       => false,
    'allowClear'     => true,
    'dropdownParent' => null,       // CSS selector e.g. '#myModal'
    'class'          => '',
    'disabled'       => false,
    'valueKey'       => 'id',        // 'id', 'user_id', or 'name'
])

@php
    $fieldId       = $id ?? 'select2_user_' . Str::random(6);
    $apiUrl        = route('dss.select2.users') . ($valueKey === 'user_id' ? '?id_type=user_id' : '');
    $isMultiple    = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
    $isRequired    = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    $isDisabled    = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    $isAllowClear  = filter_var($allowClear, FILTER_VALIDATE_BOOLEAN);

    // Normalise value to array for uniform handling in JS
    $preSelected = $value ? (is_array($value) ? $value : [$value]) : [];
@endphp

@if($label)
<label for="{{ $fieldId }}" class="form-label extra-small fw-bold text-uppercase text-muted">
    {{ $label }}
    @if($isRequired)<span class="text-danger">*</span>@endif
</label>
@endif

<select
    name="{{ $name }}{{ $isMultiple ? '[]' : '' }}"
    id="{{ $fieldId }}"
    class="form-select select2-user-picker {{ $class }}"
    {{ $isMultiple   ? 'multiple'  : '' }}
    {{ $isRequired   ? 'required'  : '' }}
    {{ $isDisabled   ? 'disabled'  : '' }}
    data-placeholder="{{ $placeholder }}"
    data-api-url="{{ $apiUrl }}"
    data-allow-clear="{{ $isAllowClear ? 'true' : 'false' }}"
    @if($dropdownParent) data-dropdown-parent="{{ $dropdownParent }}" @endif
    style="width: 100%;"
>
    {{-- Empty option required for placeholder to work in Select2 --}}
    @unless($isMultiple)
        <option value=""></option>
    @endunless
</select>

{{-- Per-instance initialisation --}}
<script>
(function () {
    function initPicker_{{ $fieldId }}() {
        if (typeof $ === 'undefined' || !$.fn.select2) return;

        const $el = $('#{{ $fieldId }}');
        const apiUrl = $el.data('api-url');

        $el.select2({
            theme: 'bootstrap-5',
            placeholder: $el.data('placeholder') || '— Select User —',
            allowClear: $el.data('allow-clear') === 'true',
            minimumInputLength: 0,
            dropdownParent: $el.data('dropdown-parent') ? $($el.data('dropdown-parent')) : $(document.body),
            width: '100%',
            ajax: {
                url: apiUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term || '' };
                },
                processResults: function (data) {
                    const flat = [];
                    (data.results || []).forEach(function (u) {
                        if (u.children && Array.isArray(u.children)) {
                            u.children.forEach(function (c) { flat.push(c); });
                        } else {
                            flat.push(u);
                        }
                    });
                    return {
                        results: flat.map(function (u) {
                            var computedId = '{{ $valueKey }}' === 'user_id' ? (u.user_id || u.id) : ('{{ $valueKey }}' === 'name' ? u.name : u.id);
                            return {
                                id: computedId,
                                text: u.text,
                                meta: u.meta || u.designation || '',
                            };
                        }),
                    };
                },
                cache: true,
            },
            templateResult: function (u) {
                if (u.loading) return u.text;
                return $('<div class="d-flex align-items-center gap-2 py-1">' +
                    '<div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" ' +
                        'style="width:26px;height:26px;font-size:11px;">' +
                        u.text.charAt(0).toUpperCase() +
                    '</div>' +
                    '<div>' +
                        '<div class="fw-semibold extra-small text-dark">' + u.text + '</div>' +
                        (u.meta ? '<div class="text-muted" style="font-size:11px;">' + u.meta + '</div>' : '') +
                    '</div>' +
                '</div>');
            },
            templateSelection: function (u) {
                return u.text || u.id;
            },
        });

        @if(count($preSelected))
        // Pre-load existing selection
        $.ajax({
            url: apiUrl,
            data: { ids: '{{ implode(",", $preSelected) }}' },
            dataType: 'json',
            success: function (data) {
                data.results.forEach(function (u) {
                    var opt = new Option(u.text, u.id, true, true);
                    $el.append(opt);
                });
                $el.trigger('change');
            },
        });
        @endif
    }

    // Init immediately if DOM is ready, else wait
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPicker_{{ $fieldId }});
    } else {
        initPicker_{{ $fieldId }}();
    }
}());
</script>
