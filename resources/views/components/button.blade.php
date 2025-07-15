@props([
    'type' => 'button',
    'color' => 'primary',
    'text' => 'Button',
    'target' => '',
    'id' => 'btn-' . uniqid(),
    'class' => '',
    'formId' => '',
])

<button 
    type="{{ $type }}" 
    id="{{ $id }}" 
    data-bs-toggle="{{ $target ? 'modal' : '' }}" 
    data-bs-target="{{ $target }}" 
    class="btn btn-{{ $color }}  {{ $class }}"
>
    <span class="btn-text">
        {{ $text }}
    </span>
    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
</button>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('{{ $id }}');

        if (!btn) return;

        btn.addEventListener('click', function () {
            const text = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');

            // Hiện loading và disable
            text.textContent = '{{ $text }}';
            spinner.classList.remove('d-none');
            btn.setAttribute('disabled', true);
            if(btn.type == 'submit') {
                const form = document.getElementById('{{ $formId }}');
                form.submit();
            } else {
                btn.click();
            }
        });
    });
</script>
@endpush
