import {
    FormControl,
    FormDescription,
    FormItem,
    FormLabel,
    FormMessage,
} from "../ui/form";

import { FormFieldRender } from "@/hmml";
import { Input } from "../ui/input";

const InputField: FormFieldRender<{ placeholder: string }> = ({
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
                    <Input placeholder={placeholder} {...field} />
                </FormControl>
                <FormMessage />
            </FormItem>
        </div>
    );
};

export default InputField;
