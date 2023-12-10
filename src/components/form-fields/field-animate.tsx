import { AnimatePresence, motion } from "framer-motion";
import { FC, ReactElement } from "react";

const FieldAnimate: FC<{ children: ReactElement; show: boolean }> = ({
    show,
    children,
}) => {
    return (
        <AnimatePresence>
            {show ? (
                <motion.div
                    initial={{
                        height: 0,
                        opacity: 0,
                        overflow: "hidden",
                    }}
                    animate={{
                        height: "auto",
                        opacity: 1,
                        overflow: "visible",
                    }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{
                        type: "ease",
                        stiffness: 100,
                    }}
                >
                    {children}
                </motion.div>
            ) : null}
        </AnimatePresence>
    );
};

export default FieldAnimate;
