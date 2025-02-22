import "./admin.css";

import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

import { ThemeProvider } from "@/components/theme-provider";
import React from "react";
import ReactDOM from "react-dom/client";
import { Toaster } from "../components/ui/toaster.tsx";
import Header from "@/components/sections/Header";
import GeneralSettings from "./GeneralSettings";
import LoginSettingsPanel from "./LoginSettings";

document.querySelectorAll("#wpbody .notice").forEach((el) => el.remove());

const queryClient = new QueryClient();

ReactDOM.createRoot(document.getElementById("gauthwp-admin")!).render(
    <React.StrictMode>
        <ThemeProvider defaultTheme="dark" storageKey="gauthwp-admin-ui-theme">
            <QueryClientProvider client={queryClient}>
                <div className="ml-[-20px] min-h-screen">
                    <div>
                        <Header />
                        <div className="container">
                            <div className="grid grid-cols-1 py-10 gap-5">
                                <GeneralSettings />
                                <LoginSettingsPanel />
                            </div>
                        </div>
                    </div>
                </div>
                <Toaster />
            </QueryClientProvider>
        </ThemeProvider>
    </React.StrictMode>
);
