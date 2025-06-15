import { defineStore } from 'pinia';

export const useNotificationStore = defineStore('notification', {
    state: (): { unreadCount: number; } => ({
        unreadCount: 0,
    }),

    actions: {
        getUnreadNotificationCount() {
            return this.unreadCount;
        },
        setUnreadNotificationCount(value: number) {
            this.unreadCount = value;
        },
        addUnreadNotificationCount(value: number = 1) {
            this.unreadCount += value;
        },
        clearNotification() {
            this.unreadCount = 0;
        },
    },
});
