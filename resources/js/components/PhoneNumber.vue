<script setup lang="ts">
import { ref } from 'vue';
import { Popover } from 'primevue';

defineProps<{
    phone: string | null;
    international_phone: string | null;
    has_whatsapp: boolean;
}>();

const option = ref();
</script>

<template>
    <template v-if="phone && international_phone">
        <button class="hover:underline text-amber-500 cursor-pointer" @click="e => option.toggle(e)">
            {{ phone }}
        </button>
        <Popover ref="option">
            <div class="list-none p-0 m-0 flex flex-col">
                <a class="flex items-center text-xs gap-1.5 p-2 hover:bg-gray-100 cursor-pointer rounded" :href="`tel:${international_phone}`">
                    <i class="pi pi-phone"></i>
                    <span class="font-semibold">{{ $t('action.call') }}</span>
                </a>
                <a class="flex items-center text-xs gap-1.5 p-2 hover:bg-gray-100 cursor-pointer rounded" :href="`https://wa.me/${international_phone}`">
                    <i class="pi pi-whatsapp"></i>
                    <span class="font-semibold">{{ $t('action.message') }}</span>
                </a>
            </div>
        </Popover>
    </template>
</template>
