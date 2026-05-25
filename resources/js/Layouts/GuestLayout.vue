<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center justify-center bg-[#e47d05] px-4 py-12">
        
        <div class="absolute inset-x-0 bottom-0 -z-10 h-1/2 rounded-t-[40px] bg-[#e47d05] sm:rounded-t-[60px]"></div>

        <div class="relative w-full max-w-md rounded-[32px] border border-orange-200 dark:border-orange-900/30 bg-white dark:bg-slate-900 px-6 py-8 shadow-2xl dark:shadow-2xl sm:px-10">
            
            <div class="flex flex-col items-center mb-6">
                <Link href="/" class="flex items-center gap-3">
                    <slot name="logo" />
                </Link>
                <div class="mt-4 h-[2px] w-full bg-[#e47d05]"></div>
            </div>

            <slot />
            
        </div>
    </div>
</template>
