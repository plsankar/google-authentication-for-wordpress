import {
    FormControl,
    FormDescription,
    FormItem,
    FormLabel,
    FormMessage,
} from "./ui/form";

import { cn } from "@/lib/utils";
import { Switch } from "./ui/switch";
import { FormFieldRender } from "@/types";

const SwitchField: FormFieldRender<{ clasName?: string }> = ({
    clasName,
    field,
    title,
    description,
}) => {
    return (
        <div className={cn(clasName, "p-5")}>
            <FormItem className="flex flex-row items-center justify-between">
                <div className="space-y-0.5">
                    <FormLabel>{title}</FormLabel>
                    <FormDescription>{description}</FormDescription>
                </div>
                <FormControl>
                    <Switch
                        checked={field.value}
                        onCheckedChange={field.onChange}
                    />
                </FormControl>
            </FormItem>
            <FormMessage />
        </div>
    );
};

export default SwitchField;
