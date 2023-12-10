import panels, { Panel } from "@/admin/panels";

import { FC } from "react";

const Sidebar = () => {
    return (
        <div className="d-flex gap-5">
            {panels.map((panel, index) => {
                return <SidebarItem panel={panel} key={index} />;
            })}
        </div>
    );
};

const SidebarItem: FC<{ panel: Panel }> = ({ panel }) => {
    return (
        <div className="flex items-center space-x-4">
            <p className="text-sm font-medium leading-none">{panel.title}</p>
            <p className="text-sm text-muted-foreground">{panel.desc}</p>
        </div>
    );
};

export default Sidebar;
