<template>
    <button 
        @click.prevent="navigate" 
        @mouseenter.prevent="blockTooltip"
        @mouseover.prevent="blockTooltip"
        @mousemove.prevent="blockTooltip"
        :class="linkClasses"
        type="button"
        :title="null"
        :href="null"
    >
        <slot></slot>
    </button>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    forceActive: {
        type: Boolean,
        default: false
    }
});

const page = usePage();

const navigate = (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (props.href) {
        router.visit(props.href);
    }
    return false;
};

const blockTooltip = (e) => {
    e.preventDefault();
    e.stopPropagation();
    window.status = '';
    if (window.defaultStatus) window.defaultStatus = '';
    return false;
};

const linkClasses = computed(() => {
    const isActive = props.forceActive || page.url === props.href;
    return [
        'nav-link',
        'group',
        { 'active-link': isActive }
    ];
});

onMounted(() => {
    // Global block tooltip
    window.defaultStatus = '';
    window.status = '';
});
</script>

<style scoped>
.nav-link {
    @apply flex items-center gap-4 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-orange-600;
    background: none;
    border: none;
    width: 100%;
    cursor: pointer;
    font-family: inherit;
    text-align: left;
    pointer-events: auto;
    position: relative;
    z-index: 1;
}

.active-link {
    @apply bg-orange-50 text-orange-700 font-bold shadow-sm shadow-orange-100/50;
}

.nav-link:hover {
    cursor: pointer;
}

.nav-link:focus {
    outline: none;
}

/* Hilangkan tooltip */
.nav-link[title],
.nav-link[data-title],
.nav-link[href] {
    pointer-events: auto;
}

/* Force hide tooltip pada semua browser */
.nav-link::before,
.nav-link::after {
    display: none !important;
    content: none !important;
}
</style>