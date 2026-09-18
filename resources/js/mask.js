/**
 * Input masks. The field carries the mask it wants as a name or a pattern; the options live
 * here, because money and percent are counted, not matched, and their options are not JSON.
 *
 * imask is imported only when a masked field is on the page.
 */
let loading = null;

const money = {
    mask: Number,
    scale: 2,
    thousandsSeparator: '.',
    radix: ',',
    padFractionalZeros: true,
    normalizeZeros: true,
    min: 0,
};

/**
 * Two masks in one field, chosen by how many digits were typed. Left to itself imask keeps the
 * first pattern that still matches, so a mobile number stopped at the tenth digit and the
 * eleventh was dropped — the landline mask was never wrong enough to be abandoned.
 */
function byLength(shorter, longer, boundary) {
    return {
        mask: [{ mask: shorter }, { mask: longer }],
        dispatch(appended, masked) {
            const digits = (masked.value + appended).replace(/\D/g, '');

            return masked.compiledMasks[digits.length > boundary ? 1 : 0];
        },
    };
}

const PRESETS = {
    /** Landline and mobile in one field. */
    phone: byLength('(00) 0000-0000', '(00) 00000-0000', 10),
    cpf: { mask: '000.000.000-00' },
    cnpj: { mask: '00.000.000/0000-00' },
    'cpf-cnpj': byLength('000.000.000-00', '00.000.000/0000-00', 11),
    cep: { mask: '00000-000' },
    date: { mask: '00/00/0000' },
    time: { mask: '00:00' },
    money,
    percent: { ...money, max: 100 },
    card: { mask: '0000 0000 0000 0000' },
};

function load() {
    loading ??= import('imask');

    return loading;
}

async function apply(element) {
    const config = JSON.parse(element.dataset.mask || '{}');

    const options = config.preset ? PRESETS[config.preset] : config.pattern ? { mask: config.pattern } : null;

    if (options === null || options === undefined) {
        return;
    }

    const { default: IMask } = await load();

    IMask(element, options);
}

export function initMasks() {
    document.querySelectorAll('[data-mask]').forEach(apply);
}
