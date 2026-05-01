import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const popupClass = 'rounded-3xl border border-slate-100 px-6 pb-7 pt-8 shadow-2xl shadow-slate-900/10';
const titleClass = 'text-xl font-black text-slate-900';
const textClass = 'text-sm leading-relaxed text-slate-500';
const buttonBase = 'mx-1 inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-bold transition-all focus:outline-none focus:ring-4';
const cancelClass = `${buttonBase} border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 focus:ring-slate-100`;
const orangeClass = `${buttonBase} bg-orange-600 text-white shadow-lg shadow-orange-100 hover:bg-orange-700 focus:ring-orange-100`;
const dangerClass = `${buttonBase} bg-rose-600 text-white shadow-lg shadow-rose-100 hover:bg-rose-700 focus:ring-rose-100`;
const successClass = `${buttonBase} bg-emerald-600 text-white shadow-lg shadow-emerald-100 hover:bg-emerald-700 focus:ring-emerald-100`;

const commonOptions = {
  buttonsStyling: false,
  reverseButtons: true,
  focusCancel: true,
  customClass: {
    popup: popupClass,
    title: titleClass,
    htmlContainer: textClass,
    confirmButton: orangeClass,
    cancelButton: cancelClass,
    input: 'rounded-2xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500',
  },
};

export const confirmDialog = async ({
  title = 'Lanjutkan tindakan ini?',
  text = '',
  html = '',
  icon = 'question',
  confirmButtonText = 'Ya, lanjutkan',
  cancelButtonText = 'Batal',
  confirmButtonClass = orangeClass,
} = {}) => {
  const result = await Swal.fire({
    ...commonOptions,
    title,
    text,
    html,
    icon,
    showCancelButton: true,
    confirmButtonText,
    cancelButtonText,
    customClass: {
      ...commonOptions.customClass,
      confirmButton: confirmButtonClass,
    },
  });

  return result.isConfirmed;
};

export const dangerConfirm = (options = {}) => confirmDialog({
  icon: 'warning',
  confirmButtonText: 'Ya, hapus',
  confirmButtonClass: dangerClass,
  ...options,
});

export const promptDialog = async ({
  title = 'Masukkan keterangan',
  text = '',
  input = 'textarea',
  inputPlaceholder = 'Tulis keterangan...',
  inputValue = '',
  confirmButtonText = 'Kirim',
  cancelButtonText = 'Batal',
  required = false,
  confirmButtonClass = orangeClass,
} = {}) => {
  const result = await Swal.fire({
    ...commonOptions,
    title,
    text,
    input,
    inputValue,
    inputPlaceholder,
    showCancelButton: true,
    confirmButtonText,
    cancelButtonText,
    inputValidator: required
      ? (value) => (!value?.trim() ? 'Keterangan wajib diisi.' : undefined)
      : undefined,
    customClass: {
      ...commonOptions.customClass,
      confirmButton: confirmButtonClass,
    },
  });

  return result.isConfirmed ? result.value : null;
};

export const dangerPrompt = (options = {}) => promptDialog({
  confirmButtonClass: dangerClass,
  ...options,
});

export const successAlert = ({
  title = 'Berhasil',
  text = '',
  timer = 1800,
} = {}) => Swal.fire({
  ...commonOptions,
  icon: 'success',
  title,
  text,
  timer,
  showConfirmButton: false,
  customClass: {
    ...commonOptions.customClass,
    confirmButton: successClass,
  },
});

export const infoAlert = ({
  title = 'Informasi',
  text = '',
  html = '',
  confirmButtonText = 'Mengerti',
} = {}) => Swal.fire({
  ...commonOptions,
  icon: 'info',
  title,
  text,
  html,
  confirmButtonText,
});
