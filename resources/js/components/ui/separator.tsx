import * as React from "react"
import { Separator as BaseSeparator } from "@base-ui-components/react/separator"

import { cn } from "@/lib/utils"

type SeparatorBaseProps = React.ComponentProps<typeof BaseSeparator>
type SeparatorProps = SeparatorBaseProps & { decorative?: boolean }

function Separator({
  className,
  orientation = "horizontal",
  decorative = true,
  ...props
}: SeparatorProps) {
  return (
    <BaseSeparator
      data-slot="separator-root"
      orientation={orientation}
      aria-hidden={decorative}
      className={cn(
        "bg-border shrink-0 data-[orientation=horizontal]:h-px data-[orientation=horizontal]:w-full data-[orientation=vertical]:h-full data-[orientation=vertical]:w-px",
        className
      )}
      {...props}
    />
  )
}

export { Separator }
