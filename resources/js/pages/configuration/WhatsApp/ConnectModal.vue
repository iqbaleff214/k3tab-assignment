<script setup lang="ts">
import { ref } from 'vue';
import { Button, Dialog } from 'primevue';
import { APIResponse, WhatsAppDevice } from '@/types';

const visible = ref<boolean>(false);
const item = ref<WhatsAppDevice>();
const qrCode = ref<string|undefined>();

let interval: number|null = null;

const open = (i: WhatsAppDevice) => {
    item.value = i;
    qrCode.value = i.qrcode;
    if (!i.qrcode)
        return;

    visible.value = true;

    interval = setInterval(check, 1000);
};

const close = () => {
    visible.value = false
    item.value = undefined;

    if (interval !== null)
        clearInterval(interval);
};

const check = () => {
    if (!item.value)
        return;

    fetch(route('json.whatsapp.check', { token: item.value.token }))
        .then((res) => res.json())
        .then((res: APIResponse<{ connected: boolean; loggedIn: boolean }>) => {
            if (res.error || !item.value)
                return;

            const { connected, loggedIn } = res.data;
            item.value.connected = connected;
            item.value.loggedIn = loggedIn;
            if (loggedIn)
                close();
        });
};

defineExpose({
    open,
})
</script>

<template>
    <Dialog
        v-model:visible="visible" modal @after-hide="close"
        :header="$t('label.connect_device')" :style="{ width: '30rem' }">
        <div class="flex flex-col gap-6">
            <img :src="qrCode" alt="QRCode">
        </div>

        <template #footer>
            <Button
                type="button" size="small" :label="$t('action.cancel')"
                severity="secondary" @click="close" />
        </template>
    </Dialog>
</template>
