import * as React from "react"
import { Tooltip as BaseTooltip } from "@base-ui-components/react/tooltip"

import { cn } from "@/lib/utils"

type TooltipProviderProps = React.ComponentProps<typeof BaseTooltip.Provider> & {
  delayDuration?: number
}

function TooltipProvider({ delay = 0, delayDuration, ...props }: TooltipProviderProps) {
  return (
    <BaseTooltip.Provider
      data-slot="tooltip-provider"
      delay={delayDuration ?? delay}
      {...props}
    />
  )
}

function Tooltip({ ...props }: React.ComponentProps<typeof BaseTooltip.Root>) {
  return (
    <TooltipProvider>
      <BaseTooltip.Root data-slot="tooltip" {...props} />
    </TooltipProvider>
  )
}

type TriggerProps = React.ComponentProps<typeof BaseTooltip.Trigger> & {
  asChild?: boolean
}

function TooltipTrigger({ asChild, children, ...props }: TriggerProps) {
  if (asChild && React.isValidElement(children)) {
    // Render the child element as the trigger element using Base UI's `render` prop
    return (
      <BaseTooltip.Trigger
        data-slot="tooltip-trigger"
        render={children as React.ReactElement<Record<string, unknown>>}
        {...props}
      />
    )
  }
  return (
    <BaseTooltip.Trigger data-slot="tooltip-trigger" {...props}>
      {children}
    </BaseTooltip.Trigger>
  )
}

type PopupProps = React.ComponentProps<typeof BaseTooltip.Popup>
type PositionerProps = React.ComponentProps<typeof BaseTooltip.Positioner>
type TooltipContentProps = PopupProps & Pick<PositionerProps, "side" | "sideOffset" | "align">

function TooltipContent({
  className,
  sideOffset = 4,
  side,
  align,
  children,
  ...props
}: TooltipContentProps) {
  return (
    <BaseTooltip.Portal>
      <BaseTooltip.Positioner sideOffset={sideOffset} side={side} align={align}>
        <BaseTooltip.Popup
          data-slot="tooltip-content"
          className={cn(
            "bg-primary text-primary-foreground z-50 max-w-sm rounded-md px-3 py-1.5 text-xs shadow-lg outline-1 outline-gray-200 transition-[transform,opacity] data-[starting-style]:scale-90 data-[starting-style]:opacity-0 data-[ending-style]:scale-90 data-[ending-style]:opacity-0",
            className
          )}
          {...props}
        >
          {children}
          <BaseTooltip.Arrow className="data-[side=bottom]:top-[-8px] data-[side=left]:right-[-13px] data-[side=left]:rotate-90 data-[side=right]:left-[-13px] data-[side=right]:-rotate-90 data-[side=top]:bottom-[-8px] data-[side=top]:rotate-180" />
        </BaseTooltip.Popup>
      </BaseTooltip.Positioner>
    </BaseTooltip.Portal>
  )
}

export { Tooltip, TooltipTrigger, TooltipContent, TooltipProvider }
