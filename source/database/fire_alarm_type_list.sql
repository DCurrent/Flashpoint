USE [EHSINFO]
GO

/****** Object:  Table [dbo].[tbl_fire_alarm_type_list]    Script Date: 2/23/2021 10:11:57 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[tbl_fire_alarm_type_list](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[label] [varchar](25) NULL,
	[active] [bit] NULL,
 CONSTRAINT [PK_tbl_fire_alarm_type_list] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO


