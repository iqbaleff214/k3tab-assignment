import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { ParsedUserAgent } from '@/types';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const formatBytes = (bytes: number): string => {
    if (bytes === 0 || bytes === null || bytes === undefined) return '0 B';

    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const size = parseFloat((bytes / Math.pow(k, i)).toFixed(2));
    return `${size} ${sizes[i]}`;
};

export const estimatedFormatBytes = (bytes: number): string => {
    if (bytes === 0 || bytes === null || bytes === undefined) return '0 B';

    const k = 1000;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const size = parseFloat((bytes / Math.pow(k, i)).toFixed(2));
    return `${size} ${sizes[i]}`;
};

export const dateMonthYear = (value: Date | string | null, locale: string = 'en-GB'): string => {
    if (!value)
        return '-';

    const date = new Date(value);

    return date.toLocaleDateString(locale, {
        month: 'short', year: 'numeric',
    });
};

export const dateHumanFormat = (value: Date | string | null, addHour: number = 0, locale: string = 'en-GB'): string => {
    if (!value)
        return '-';

    const date = new Date(value);
    const finalDate = new Date(date.getTime() + addHour * 60 * 60 * 1000);

    return finalDate.toLocaleDateString(locale, {
        weekday: 'short', day: '2-digit',
        month: 'short', year: 'numeric',
    });
};

export const dateHumanFormatWithTime = (value: Date | string | null, addHour: number = 0, locale: string = 'en-GB') => {
    if (!value)
        return '-';

    const date = new Date(value);
    const finalDate = new Date(date.getTime() + addHour * 60 * 60 * 1000);

    return finalDate.toLocaleDateString(locale, {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
};

export const dateHumanSmartFormat = (value: Date | string | null, locale: string = 'en-GB'): string => {
    if (!value)
        return '-';

    const now = new Date();
    const date = new Date(value);

    const isToday =
        date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear();

    const isThisYear = date.getFullYear() === now.getFullYear();

    if (isToday) {
        return date.toLocaleTimeString(locale, {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        });
    }

    if (isThisYear) {
        return date.toLocaleDateString(locale, {
            day: '2-digit',
            month: 'short',
        });
    }

    return date.toLocaleDateString(locale, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

export const timeAgo = (value: Date | string, locale: string = 'en'): string => {
    const rtf = new Intl.RelativeTimeFormat(locale, { numeric: 'auto' });
    const now = new Date();
    const date = new Date(value);
    const secondsElapsed = Math.floor((now.getTime() - date.getTime()) / 1000);

    const intervals: { unit: Intl.RelativeTimeFormatUnit; seconds: number }[] = [
        { unit: 'year', seconds: 31536000 },
        { unit: 'month', seconds: 2592000 },
        { unit: 'week', seconds: 604800 },
        { unit: 'day', seconds: 86400 },
        { unit: 'hour', seconds: 3600 },
        { unit: 'minute', seconds: 60 },
        { unit: 'second', seconds: 1 },
    ];

    for (const { unit, seconds } of intervals) {
        if (Math.abs(secondsElapsed) >= seconds || unit === 'second') {
            const value = Math.floor(secondsElapsed / seconds);
            return rtf.format(-value, unit);
        }
    }

    return '';
};


export const rupiahCurrency = (value: number): string => {
    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    });
    return formatter.format(value);
};

export const parseUserAgent = (ua: string): ParsedUserAgent => {
    let os = 'Unknown';
    let browser = 'Unknown';
    let version = 'Unknown';
    let deviceType: ParsedUserAgent['deviceType'] = 'Unknown';

    // OS
    if (/Windows NT/.test(ua)) os = 'Windows';
    else if (/Mac OS X/.test(ua)) os = 'Mac OS';
    else if (/Android/.test(ua)) os = 'Android';
    else if (/iPhone|iPad|iPod/.test(ua)) os = 'iOS';
    else if (/Linux/.test(ua)) os = 'Linux';

    // Browser & Version
    if (/Chrome\/([0-9.]+)/.test(ua)) {
        browser = 'Chrome';
        version = ua.match(/Chrome\/([0-9.]+)/)?.[1] ?? 'Unknown';
    } else if (/Safari\/([0-9.]+)/.test(ua) && /Version\/([0-9.]+)/.test(ua)) {
        browser = 'Safari';
        version = ua.match(/Version\/([0-9.]+)/)?.[1] ?? 'Unknown';
    } else if (/Firefox\/([0-9.]+)/.test(ua)) {
        browser = 'Firefox';
        version = ua.match(/Firefox\/([0-9.]+)/)?.[1] ?? 'Unknown';
    } else if (/MSIE ([0-9.]+)/.test(ua)) {
        browser = 'Internet Explorer';
        version = ua.match(/MSIE ([0-9.]+)/)?.[1] ?? 'Unknown';
    } else if (/Edg\/([0-9.]+)/.test(ua)) {
        browser = 'Edge';
        version = ua.match(/Edg\/([0-9.]+)/)?.[1] ?? 'Unknown';
    }

    // Device type
    if (/Mobile/.test(ua)) deviceType = 'Mobile';
    else if (/Tablet/.test(ua)) deviceType = 'Tablet';
    else if (/Macintosh|Windows|X11|Linux/.test(ua)) deviceType = 'Desktop';

    return { os, browser, version, deviceType };
}

export const getDepth = (data: any, key?: string): number => {
    if (typeof data !== 'object' || data === null) return 0;

    if (key && Array.isArray(data[key])) {
        return 1 + Math.max(0, ...data[key].map((child: any) => getDepth(child, key)));
    }

    if (Array.isArray(data)) {
        return 1 + Math.max(0, ...data.map((item: any) => getDepth(item, key)));
    }

    return 1 + Math.max(0, ...Object.values(data).map((value: any) => getDepth(value, key)));
};

export const downloadFile = (url: string, filename: string) => {
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
};

export const isHttpUrl = (str: any): boolean => {
    try {
        if (typeof str !== 'string')
            return false;

        const url = new URL(str);
        return url.protocol === "http:" || url.protocol === "https:";
    } catch {
        return false;
    }
}

export const shuffleArray = (array: any[]): any[] => {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
}
