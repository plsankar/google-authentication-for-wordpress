import { ModeToggle } from "../mode-toggle";

const Header = () => {
    return (
        <div className="py-5 border-b bg-background">
            <div className="container">
                <div className="flex justify-between">
                    {/* <div className="text-2xl">Wordpress Admin</div> */}
                    <div className="ml-auto">
                        <ModeToggle />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Header;
