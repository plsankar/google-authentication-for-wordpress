import api, { ApiActionResult } from "@/admin/api";
import {
    UseMutationResult,
    useMutation,
    useQuery,
    useQueryClient,
} from "@tanstack/react-query";
import { AxiosError, AxiosResponse, isAxiosError } from "axios";
import { Alert, AlertDescription, AlertTitle } from "./ui/alert";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "./ui/card";

import { motion } from "framer-motion";
import { AlertCircle } from "lucide-react";
import { FC } from "react";
import { Skeleton } from "./ui/skeleton";
import { WP_Error } from "@/types";
import { useToast } from "./ui/use-toast";

export interface PanelChild<D> {
    data: D;
    mutation: UseMutationResult<
        AxiosResponse<ApiActionResult<D>>,
        AxiosError<WP_Error<D>>,
        D
    >;
}

const withPanel = <D, T>(
    WrappedComponent: FC<T & PanelChild<D>>,
    restEndpoint: string,
    title: string,
    descrption?: string | null
) => {
    const WithPanel: FC<T> = (props) => {
        const queryClient = useQueryClient();
        const { toast } = useToast();

        const query = useQuery<AxiosResponse<D>, AxiosError<WP_Error<D>>>({
            queryKey: ["panel", restEndpoint],
            queryFn: ({ signal }) =>
                api.get(restEndpoint, {
                    signal,
                }),
        });

        const { isLoading, data, error } = query;

        const mutation = useMutation<
            AxiosResponse<ApiActionResult<D>>,
            AxiosError<WP_Error<D>>,
            D
        >({
            mutationKey: ["panel", restEndpoint],
            mutationFn: (data: D) => {
                return api.put(restEndpoint, data);
            },
            onSuccess: () => {
                queryClient.invalidateQueries(["panel", restEndpoint]);
            },
            onError(error, _variables, _context) {
                if (isAxiosError(error)) {
                    var axiosError = error as AxiosError<{ message: string }>;
                    if (axiosError.response?.status == 403) {
                        toast({
                            title: "You are not logged in anymore!.",
                        });
                        return;
                    }
                    if (
                        axiosError.response &&
                        axiosError.response?.data &&
                        axiosError.response.data.message
                    ) {
                        toast({
                            title: axiosError.response.data.message,
                        });
                        return;
                    }
                }
                toast({
                    title: "Oops!, an unknown occured!",
                });
            },
        });

        return (
            <motion.div
                layout
                initial={{ opacity: 0, y: 100 }}
                transition={{
                    type: "spring",
                    stiffness: 100,
                    delay: 0.5,
                    when: "afterChildren",
                }}
                whileInView={{ opacity: 1, y: 0 }}
            >
                <Card className="relative overflow-hidden shadow-none">
                    <CardHeader>
                        <CardTitle className="font-serif font-normal text-xl">
                            {title}
                        </CardTitle>
                        {descrption ? (
                            <CardDescription>{descrption}</CardDescription>
                        ) : null}
                    </CardHeader>
                    {!isLoading && data ? (
                        <WrappedComponent
                            {...props}
                            data={query.data!.data}
                            mutation={mutation}
                        />
                    ) : null}
                    {isLoading ? (
                        <CardContent>
                            <div className="flex flex-row items-center justify-between rounded-lg border p-3">
                                <div className="space-y-1 w-full">
                                    <Skeleton className="h-5 w-full" />
                                    <Skeleton className="h-5 w-full" />
                                </div>
                            </div>
                        </CardContent>
                    ) : null}
                    {error ? (
                        <CardContent>
                            <Alert>
                                <AlertCircle className="h-4 w-4" />
                                <AlertTitle>{error.code}</AlertTitle>
                                <AlertDescription>
                                    {error.message}
                                </AlertDescription>
                            </Alert>
                        </CardContent>
                    ) : null}
                </Card>
            </motion.div>
        );
    };

    return WithPanel;
};

export default withPanel;
