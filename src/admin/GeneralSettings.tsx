import * as z from "zod";

import { CardContent, CardFooter } from "@/components/ui/card";
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import withPanel, { PanelChild } from "@/components/withPanel";
import { FC, useEffect, useMemo } from "react";

import FieldAnimate from "@/components/form-fields/field-animate";
import TextareaField from "@/components/form-fields/textarea-field";
import Spinner from "@/components/spinner";
import SwitchField from "@/components/switch-field";
import { Button } from "@/components/ui/button";
import { ToastAction } from "@/components/ui/toast";
import { useToast } from "@/components/ui/use-toast";
import { getAdminAjaxUrl } from "@/lib/utils";
import { zodResolver } from "@hookform/resolvers/zod";
import { CopyToClipboard } from "react-copy-to-clipboard";
import { useForm } from "react-hook-form";

const FormSchema = z
    .object({
        gauthwp_enabled: z.boolean().default(false),
        gauthwp_client_id: z.string().optional(),
        gauthwp_client_secret: z.string().optional(),
    })
    .superRefine((values, ctx) => {
        if (values.gauthwp_enabled == true) {
            if (
                values.gauthwp_client_id == undefined ||
                values.gauthwp_client_id == ""
            ) {
                ctx.addIssue({
                    message: "Client ID can't be empty",
                    code: z.ZodIssueCode.custom,
                    path: ["gauthwp_client_id"],
                });
            }
        }

        if (values.gauthwp_enabled == true) {
            if (
                values.gauthwp_client_secret == undefined ||
                values.gauthwp_client_secret == ""
            ) {
                ctx.addIssue({
                    code: z.ZodIssueCode.custom,
                    message: "Client Secret can't be empty",
                    path: ["gauthwp_client_secret"],
                });
            }
        }
    });

type GeneralSettingsData = z.infer<typeof FormSchema>;

interface GeneralSettingsProps {}

const GeneralSettings: FC<
    GeneralSettingsProps & PanelChild<GeneralSettingsData>
> = ({ data, mutation }) => {
    const form = useForm<GeneralSettingsData>({
        resolver: zodResolver(FormSchema),
        defaultValues: data,
    });
    const { toast } = useToast();

    const hasChanged = useMemo(() => {
        return JSON.stringify(data) !== JSON.stringify(form.getValues());
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [data, form.getValues()]);

    useEffect(() => {
        form.reset(data);
    }, [data]);

    return (
        <Form {...form}>
            <form onSubmit={form.handleSubmit((data) => mutation.mutate(data))}>
                <CardContent className="p-0 divide-y border-y">
                    <FormField
                        control={form.control}
                        name="gauthwp_enabled"
                        render={(props) => (
                            <SwitchField
                                {...props}
                                title="Enable"
                                description="Enable Google Sign In"
                            />
                        )}
                    />
                    <FieldAnimate show={form.getValues("gauthwp_enabled")}>
                        <div className="p-5">
                            <FormLabel>Open Google Cloud Console</FormLabel>
                            <div className="mt-3">
                                <Button asChild>
                                    <a
                                        href="https://console.cloud.google.com/apis/credentials/consent"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Open
                                    </a>
                                </Button>
                            </div>
                        </div>
                    </FieldAnimate>
                    <FieldAnimate show={form.getValues("gauthwp_enabled")}>
                        <div className="p-5">
                            <FormItem>
                                <FormLabel>Redirect Url</FormLabel>
                                <FormDescription>
                                    Copy and paste the following url in the
                                    Google Console.
                                </FormDescription>
                                <FormControl>
                                    <CopyToClipboard
                                        text={getAdminAjaxUrl(
                                            "gauthwp_callback"
                                        )}
                                        onCopy={() => {
                                            toast({
                                                title: "Copied!",
                                                description:
                                                    "Redirect URL has been copied!",
                                                action: (
                                                    <ToastAction
                                                        onClick={() =>
                                                            window.open(
                                                                "https://console.cloud.google.com/apis/credentials/consent/edit"
                                                            )
                                                        }
                                                        altText="Open Console"
                                                    >
                                                        Open Console
                                                    </ToastAction>
                                                ),
                                            });
                                        }}
                                    >
                                        <Button
                                            type="button"
                                            variant="outline"
                                            className="w-full justify-start"
                                        >
                                            {getAdminAjaxUrl(
                                                "gauthwp_callback"
                                            )}
                                        </Button>
                                    </CopyToClipboard>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </div>
                    </FieldAnimate>
                    <FieldAnimate show={form.getValues("gauthwp_enabled")}>
                        <FormField
                            control={form.control}
                            name="gauthwp_client_id"
                            render={(props) => (
                                <TextareaField
                                    placeholder={""}
                                    {...props}
                                    title="OAuth Client ID"
                                    description=""
                                />
                            )}
                        />
                    </FieldAnimate>
                    <FieldAnimate show={form.getValues("gauthwp_enabled")}>
                        <FormField
                            control={form.control}
                            name="gauthwp_client_secret"
                            render={(props) => (
                                <TextareaField
                                    placeholder={""}
                                    {...props}
                                    title="OAuth Client Secret"
                                    description=""
                                />
                            )}
                        />
                    </FieldAnimate>
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

const GeneralSettingsPanel = withPanel<
    GeneralSettingsData,
    GeneralSettingsProps
>(GeneralSettings, "/settings", "General");

export default GeneralSettingsPanel;
