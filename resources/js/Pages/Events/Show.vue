<template>
  <div class="min-h-screen bg-white font-sans antialiased">
    <Navbar @open-auth="showAuthModal = true" />

    <main class="max-w-6xl mx-auto px-4 py-28 md:py-32">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
          
          <div class="flex items-center gap-4">
            <span class="inline-block bg-orange-500 text-white text-xs font-bold px-4 py-1.5 rounded-full tracking-wide">
              {{ event.category_label || 'Event' }}
            </span>
            
            <div class="flex items-center gap-1.5 text-xs font-bold">
              <span 
                class="w-2 h-2 rounded-full"
                :class="event.is_running ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'"
              ></span>
              <span :class="event.is_running ? 'text-emerald-600' : 'text-gray-500'">
                {{ event.status_label }}
              </span>
            </div>
          </div>

          <h1 class="text-3xl md:text-4xl font-extrabold text-slate-950 leading-tight">
            {{ event.title }}
          </h1>

          <div class="grid grid-cols-2 gap-4 border-y border-gray-100 py-4 my-2">
            <div>
              <p class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Mulai Event</p>
              <p class="text-sm md:text-base font-black text-slate-900 mt-0.5">
                {{ formatDate(event.starts_at) }}
              </p>
            </div>
            <div class="border-l border-gray-100 pl-4">
              <p class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Selesai Event</p>
              <p class="text-sm md:text-base font-black text-slate-900 mt-0.5">
                {{ formatDate(event.ends_at) }}
              </p>
            </div>
          </div>

          <div class="w-full pt-2">
            <div class="overflow-hidden bg-slate-100 rounded-sm border border-gray-100">
              <img 
                v-if="event.image_url" 
                :src="event.image_url" 
                :alt="event.title" 
                class="w-full h-auto object-cover max-h-[420px]"
              />
              <div v-else class="flex h-[320px] items-center justify-center bg-slate-50 text-sm font-bold text-gray-400">
                Dokumentasi Banner Event Literasi
              </div>
            </div>
            <p class="text-center text-xs text-gray-400 mt-2 italic">
              Banner Resmi Kegiatan {{ event.title }}
            </p>
          </div>

          <article class="prose max-w-none pt-2 space-y-4">
            <p class="text-slate-800 text-sm leading-relaxed text-justify whitespace-pre-line font-medium">
              {{ event.description }}
            </p>
          </article>

          <section class="border border-gray-200 rounded-md p-5 bg-white space-y-3">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-gray-100 pb-2">
              Ketentuan Event
            </h3>
            <div class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">
              {{ event.terms || 'Ketentuan event akan diinformasikan oleh admin perpustakaan.' }}
            </div>
          </section>

          <section v-if="event.winner" class="border border-gray-200 rounded-md p-5 flex flex-col sm:flex-row items-center gap-4 bg-white">
            <img :src="avatarFor(event.winner)" :alt="event.winner.name" class="h-14 w-14 rounded-full object-cover border border-gray-200 shadow-sm shrink-0">
            <div class="text-center sm:text-left space-y-0.5">
              <span class="text-[10px] font-bold text-orange-500 uppercase tracking-widest block">Pemenang Event 🎉</span>
              <h4 class="text-sm font-bold text-slate-900">{{ event.winner.name }}</h4>
              <p class="text-xs text-gray-500 font-medium">Selamat atas pencapaian literasi luar biasa yang telah diraih!</p>
            </div>
          </section>

        </div>

        <div class="lg:col-span-1">
          <div class="border border-gray-200 rounded-md p-6 sticky top-6 bg-white space-y-6">
            
            <div class="space-y-1">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Status Kegiatan</span>
              <h3 class="text-lg font-black text-slate-900">
                {{ participation ? 'Sudah Berpartisipasi' : event.is_running ? 'Terbuka Untuk Siswa' : 'Pendaftaran Ditutup' }}
              </h3>
              
              <p v-if="event.category === 'point'" class="text-xs text-gray-500 leading-normal pt-1">
                Poin yang dikumpulkan selama periode pendaftaran: <span class="font-bold text-orange-500">{{ userEventPoints }} poin</span>.
              </p>
              <p v-else class="text-xs text-gray-500 leading-normal pt-1">
                Pengiriman berkas lembar karya tulis hanya diperbolehkan satu kali dalam format dokumen resmi PDF.
              </p>
            </div>

            <div class="pt-4 border-t border-gray-100">
              <div v-if="authRequired" class="text-center space-y-3">
                <p class="text-xs font-semibold text-gray-600">Silakan masuk akun terlebih dahulu untuk mengikuti kegiatan ini.</p>
                <button 
                  @click="showAuthModal = true" 
                  class="w-full bg-orange-500 text-white text-xs font-bold py-3 px-4 rounded-md hover:bg-orange-600 transition tracking-wide"
                >
                  Masuk Akun
                </button>
              </div>

              <template v-else>
                <button
                  v-if="event.category === 'point'"
                  @click="joinPointEvent"
                  :disabled="!event.is_running || participation || form.processing"
                  class="w-full text-xs font-bold py-3 px-4 rounded-md transition tracking-wide text-center block disabled:cursor-not-allowed"
                  :class="(!event.is_running || participation) ? 'bg-gray-100 text-gray-400 border border-gray-200' : 'bg-orange-500 text-white hover:bg-orange-600'"
                >
                  <template v-if="!event.is_running">Pendaftaran Ditutup</template>
                  <template v-else-if="participation">Sudah Berpartisipasi</template>
                  <template v-else>Daftar Partisipasi</template>
                </button>

                <form v-else class="space-y-4" @submit.prevent="submitKarya">
                  <div v-if="event.is_running && !participation">
                    <label class="block rounded-md border-2 border-dashed border-gray-300 bg-slate-50 hover:bg-slate-100/70 transition p-4 text-center cursor-pointer">
                      <input 
                        type="file" 
                        accept="application/pdf" 
                        class="hidden" 
                        @change="handleSubmission"
                      >
                      <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-700 block">
                          {{ form.submission ? form.submission.name : 'Pilih Berkas PDF Karya' }}
                        </span>
                        <span class="text-[10px] text-gray-400 block" v-if="!form.submission">
                          Klik untuk menelusuri file dokumen
                        </span>
                      </div>
                    </label>
                    <p v-if="form.errors.submission" class="text-[11px] font-medium text-rose-600 mt-1.5">{{ form.errors.submission }}</p>
                  </div>

                  <button
                    type="submit"
                    :disabled="!event.is_running || participation || form.processing"
                    class="w-full text-xs font-bold py-3 px-4 rounded-md transition tracking-wide disabled:cursor-not-allowed"
                    :class="(!event.is_running || participation) ? 'bg-gray-100 text-gray-400 border border-gray-200' : 'bg-orange-500 text-white hover:bg-orange-600'"
                  >
                    <template v-if="!event.is_running">Event Sudah Ditutup</template>
                    <template v-else-if="participation">Karya Dokumen Berhasil Dikirim</template>
                    <template v-else-if="form.processing">Mengirim Berkas...</template>
                    <template v-else>Kirim Lembar Karya</template>
                  </button>
                </form>
              </template>
            </div>

          </div>
        </div>

      </div>
    </main>

    <FooterSection />
    <AuthModal :show="showAuthModal" @close="showAuthModal = false" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import Navbar from '@/Components/Navbar.vue';
import FooterSection from '@/Components/FooterSection.vue';
import AuthModal from '@/Components/AuthModal.vue';

const props = defineProps({
  event: { type: Object, required: true },
  participation: { type: Object, default: null },
  userEventPoints: { type: Number, default: 0 },
  authRequired: { type: Boolean, default: false },
});

const showAuthModal = ref(false);
const form = useForm({ submission: null });

function handleSubmission(event) {
  form.submission = event.target.files?.[0] || null;
}

// Logika submit data event poin
function joinPointEvent() {
  if (!props.event.is_running || props.participation) return;

  form.post(route('events.participate', props.event.slug), {
    preserveScroll: true,
    onSuccess: () => Swal.fire({
      title: 'Berhasil',
      text: 'Kamu sudah terdaftar berpartisipasi di event ini.',
      icon: 'success',
      confirmButtonColor: '#f97316'
    }),
  });
}

// Logika submit lembar karya PDF
async function submitKarya() {
  if (!props.event.is_running || props.participation) return;

  if (!form.submission) {
    Swal.fire({
      title: 'Pilih Berkas PDF',
      text: 'Silakan pilih file lembar karya dalam bentuk PDF terlebih dahulu.',
      icon: 'warning',
      confirmButtonColor: '#f97316'
    });
    return;
  }

  const result = await Swal.fire({
    title: 'Kirim karya ini?',
    text: 'Karya hanya bisa dikirim satu kali. Pastikan file yang dipilih sudah benar.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#6b7280',
    cancelButtonText: 'Cek Kembali',
    confirmButtonText: 'Ya, Kirim',
  });

  if (result.isConfirmed) {
    form.post(route('events.participate', props.event.slug), {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => Swal.fire({
        title: 'Berhasil',
        text: 'Lembar karya tulis kamu berhasil diunggah.',
        icon: 'success',
        confirmButtonColor: '#f97316'
      }),
    });
  }
}

function avatarFor(user) {
  return user?.profile_photo_url
    || (user?.profile_photo_path ? `/storage/${user.profile_photo_path}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&background=f97316&color=fff`);
}

function formatDate(date) {
  if (!date) return '-';
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(date));
}
</script>

<style scoped>
.prose :deep(p) {
  @apply mb-4 text-slate-700 text-sm leading-relaxed text-justify;
}
</style>