USE [EHSINFO]
GO

/****** Object:  Table [dbo].[dc_flashpoint_tbl_fire_alarm_device_list]    Script Date: 2/23/2021 2:17:34 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[dc_flashpoint_tbl_fire_alarm_device_list](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[label] [varchar](40) NULL,
	[active] [bit] NULL,
 CONSTRAINT [dc_flashpoint_PK_tbl_fire_alarm_device_list] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO


