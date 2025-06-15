<script setup lang="ts">
import { Paginate } from '@/types';
import Paginator, { PageState } from 'primevue/paginator';
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface Props {
    paginator: Paginate<any>;
}

const props = defineProps<Props>();
const first = ref(0);

const pageChange = (event: PageState) => {
    const url = new URL(window.location.href);
    url.searchParams.set("page", (event.page + 1).toString());
    url.searchParams.set("size", event.rows.toString());
    router.get(url.toString(), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

onMounted(() => {
    first.value = (props.paginator.current_page - 1) * props.paginator.per_page
});
</script>

<template>
    <Paginator
        @page="pageChange" v-if="paginator.total > 0"
        :first :rows-per-page-options="[10, 20, 30, 40, 50]"
        :total-records="paginator.total" :rows="paginator.per_page" >
        <template #start>
            <span class="text-sm">
                {{ $t('label.show_to_of', { from: paginator.from, to: paginator.to, total: paginator.total }) }}
            </span>
        </template>
    </Paginator>
</template>
