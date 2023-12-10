import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const getAdminAjaxUrl = (action: string) => {
    const url = new URL(window.gauthwp_admin.ajaxUrl);
    url.searchParams.append("action", action);
    return url.href;
};
