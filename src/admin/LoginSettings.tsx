import * as z from "zod";

import { CardContent, CardFooter } from "@/components/ui/card";
import { FC, useEffect, useMemo } from "react";
import { Form, FormField } from "@/components/ui/form";
import withPanel, { PanelChild } from "@/components/withPanel";

import { Button } from "@/components/ui/button";
import FieldAnimate from "@/components/form-fields/field-animate";
import Spinner from "@/components/spinner";
import SwitchField from "@/components/switch-field";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";

const FormSchema = z.object({
    slwg_google_show_on_login: z.boolean().default(false),
});

type LoginSettingsData = z.infer<typeof FormSchema>;

interface LoginSettingsProps {}

const LoginSettings: FC<LoginSettingsProps & PanelChild<LoginSettingsData>> = ({
    data,
    mutation,
}) => {
    const form = useForm<LoginSettingsData>({
        resolver: zodResolver(FormSchema),
        defaultValues: data,
    });

    const hasChanged = useMemo(() => {
        return JSON.stringify(data) !== JSON.stringify(form.getValues());
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [data, form.getValues()]);

    useEffect(() => {
        form.reset(data);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [data]);

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit((data) => mutation.mutate(data))}>
                <CardContent className="p-0 divide-y border-y">
                    <FormField
                        control={form.control}
                        name="slwg_google_show_on_login"
                        render={(props) => (
                            <SwitchField
                                {...props}
                                title="Show On Login"
                                description="Shows Google sign in button on login screen"
                            />
                        )}
                    />
                </CardContent>
                <FieldAnimate show={hasChanged == true}>
                    <CardFooter className="flex justify-between pt-5">
                        <Button
                            variant="outline"
                            disabled={mutation.isLoading}
                            onClick={() => form.reset()}
                        >
                            Reset
                        </Button>
                        <Button type="submit" disabled={mutation.isLoading}>
                            {mutation.isLoading ? <Spinner /> : <>Update</>}
                        </Button>
                    </CardFooter>
                </FieldAnimate>
            </form>
        </Form>
    );
};

const LoginSettingsPanel = withPanel<LoginSettingsData, LoginSettingsProps>(
    LoginSettings,
    "/settings",
    "Login",
);

export default LoginSettingsPanel;
