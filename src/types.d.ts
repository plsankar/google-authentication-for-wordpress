import {
    ControllerFieldState,
    ControllerRenderProps,
    FieldPath,
    FieldValues,
    UseFormStateReturn,
} from "react-hook-form";

import { ReactElement } from "react";

declare global {
    interface Window {
        slwg_admin: {
            ajaxurl: string;
            adminurl: string;
            pluginurl: string;
            pluginAdminUrl: string;
            ajaxUrl: string;
            rest_url: string;
            rest_nonce: string;
        };
        slwg_login_google: {
            args: {
                pluginurl: string;
                show_on_login: boolean;
                authUrl: string;
            };
        };
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        wp: any;
    }
}

type FormFieldRender<FProps> = <
    TFieldValues extends FieldValues = FieldValues,
    TName extends FieldPath<TFieldValues> = FieldPath<TFieldValues>
>({
    field,
    fieldState,
    formState,
    title,
    description,
}: {
    field: ControllerRenderProps<TFieldValues, TName>;
    fieldState: ControllerFieldState;
    formState: UseFormStateReturn<TFieldValues>;
    title: string | ReactElement;
    description?: string | ReactElement;
} & FProps) => React.ReactElement;

type WP_Error<TData> = {
    code: string;
    message: string;
    data: TData;
};
