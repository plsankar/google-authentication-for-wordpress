import {
    FormDescription,
    FormItem,
    FormLabel,
    FormMessage,
    useFormField,
} from "../ui/form";

import { FormFieldRender } from "@/hmml";
import { cn } from "@/lib/utils";
import { Button } from "../ui/button";

const ImageField: FormFieldRender<{ className?: string }> = ({
    field,
    title,
    description,
    className,
}) => {
     
    function selectImage() {
        const { wp } = window;
        const frame = wp.media({
            title: "Select or Upload Media Of Your Chosen Persuasion",
            button: {
                text: "Use this media",
            },
            multiple: false, // Set to true to allow multiple files to be selected
        });
        frame.on("select", function () {
            const attachment = frame.state().get("selection").first().toJSON();
            console.log(attachment);
            field.onChange({
                url: attachment.url,
                id: attachment.id,
            });
        });

        frame?.open();
    }
    console.log(field.value);
    const { error } = useFormField();
    console.log(error);

    return (
        <div className={cn("p-5", className)}>
            <FormItem className="flex flex-col gap-5">
                <div className="space-y-0.5">
                    <FormLabel>{title}</FormLabel>
                    <FormDescription>{description}</FormDescription>
                </div>
                {field.value.id ? (
                    <div className="flex flex-col gap-5">
                        <img
                            width={150}
                            className="w-50 h-auto"
                            src={field.value.url}
                        />
                        <div>
                            <Button
                                type="button"
                                className="outline"
                                onClick={() => {
                                    field.onChange({
                                        url: null,
                                        id: null,
                                    });
                                }}
                            >
                                Remove
                            </Button>
                        </div>
                    </div>
                ) : (
                    <div>
                        <Button type="button" onClick={selectImage}>
                            Select
                        </Button>
                    </div>
                )}
            </FormItem>
            <FormMessage />
        </div>
    );
};

export default ImageField;
