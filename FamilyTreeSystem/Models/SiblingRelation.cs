using System.ComponentModel;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using FamilyTreeSystem.Models.Enums;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 兄弟姐妹关系模型
    /// </summary>
    [Table("SiblingRelations")]
    public class SiblingRelation
    {
        /// <summary>
        /// 主键ID
        /// </summary>
        [Key]
        [DisplayName("ID")]
        public int Id { get; set; }

        /// <summary>
        /// 成员1ID
        /// </summary>
        [DisplayName("成员1ID")]
        public int Member1Id { get; set; }

        /// <summary>
        /// 成员2ID
        /// </summary>
        [DisplayName("成员2ID")]
        public int Member2Id { get; set; }

        /// <summary>
        /// 成员1对成员2的关系类型
        /// </summary>
        [DisplayName("关系类型")]
        public RelationshipType RelationshipType { get; set; }

        /// <summary>
        /// 创建时间
        /// </summary>
        public DateTime CreatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 成员1导航属性
        /// </summary>
        [ForeignKey("Member1Id")]
        public FamilyMember Member1 { get; set; } = null!;

        /// <summary>
        /// 成员2导航属性
        /// </summary>
        [ForeignKey("Member2Id")]
        public FamilyMember Member2 { get; set; } = null!;

        /// <summary>
        /// 获取关系类型中文显示
        /// </summary>
        public string RelationshipTypeDisplay => RelationshipType switch
        {
            RelationshipType.EldestBrother => "长兄",
            RelationshipType.SecondBrother => "次兄",
            RelationshipType.ThirdBrother => "三兄",
            RelationshipType.FourthBrother => "四兄",
            RelationshipType.EldestSister => "长姐",
            RelationshipType.SecondSister => "次姐",
            RelationshipType.ThirdSister => "三姐",
            RelationshipType.FourthSister => "四姐",
            RelationshipType.SecondYoungerBrother => "次弟",
            RelationshipType.ThirdYoungerBrother => "三弟",
            RelationshipType.FourthYoungerBrother => "四弟",
            RelationshipType.YoungestBrother => "小弟",
            RelationshipType.SecondYoungerSister => "次妹",
            RelationshipType.ThirdYoungerSister => "三妹",
            RelationshipType.FourthYoungerSister => "四妹",
            RelationshipType.YoungestSister => "小妹",
            _ => "未知关系"
        };
    }
}