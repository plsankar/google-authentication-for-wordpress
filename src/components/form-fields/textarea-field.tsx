import { FormFieldRender } from "@/types";
import {
    FormControl,
    FormDescription,
    FormItem,
    FormLabel,
    FormMessage,
} from "../ui/form";

import { Textarea } from "../ui/textarea";

const TextareaField: FormFieldRender<{ placeholder: string }> = ({
    field,
    title,
    description,
    placeholder,
}) => {
    return (
        <div className="p-5">
            <FormItem>
                <FormLabel>{title}</FormLabel>
                <FormDescription>{description}</FormDescription>
                <FormControl>
                    <Textarea placeholder={placeholder} {...field} />
                </FormControl>
                <FormMessage />
            </FormItem>
        </div>
    );
};

export default TextareaField;
